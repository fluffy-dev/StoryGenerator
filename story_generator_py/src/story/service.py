import logging
from datetime import datetime, timezone
from typing import AsyncGenerator

from openai import AsyncOpenAI, APIError, APIConnectionError, RateLimitError
from fastapi import HTTPException, status

from src.config import settings
from src.story.schemas import StoryRequest

logger = logging.getLogger(__name__)


class StoryService:
    """Service responsible for generating stories via LLM."""

    def __init__(self):
        """Initializes the service with configuration settings.
        """
        self._settings = settings
        self._client = AsyncOpenAI(api_key=self._settings.OPENAI_API_KEY)

    def _construct_prompt(self, request: StoryRequest) -> str:
        """Constructs the system prompt for the LLM.

        Args:
            request: The validated request object containing generation parameters.

        Returns:
            str: The formatted prompt string.
        """
        lang_map = {
            "ru": "русском",
            "kk": "казахском"
        }
        language_full = lang_map.get(request.language, "русском")
        characters_str = ", ".join(request.characters)

        return (
            f"Напиши добрую и поучительную сказку на {language_full} языке "
            f"для ребёнка возраста {request.age} лет. "
            f"Главные персонажи: {characters_str}. "
            f"Используй Markdown форматирование. "
            f"Добавь заголовок в начале."
        )

    def _generate_metadata_header(self, request: StoryRequest) -> str:
        """Generates a markdown header with story metadata.

        Args:
            request: The validated request object.

        Returns:
            str: Markdown string with metadata.
        """
        lang_full = "Русский" if request.language == "ru" else "Казахский"
        chars = ", ".join(request.characters)
        return (
            f"# Сказка для {request.age}-летнего ребёнка\n"
            f"**Язык:** {lang_full}\n"
            f"**Персонажи:** {chars}\n\n"
            f"---\n\n"
        )

    def _generate_footer(self) -> str:
        """Generates a markdown footer with timestamp.

        Returns:
            str: Markdown string with timestamp.
        """
        now = datetime.now(timezone.utc).strftime("%Y-%m-%dT%H:%M:%SZ")
        return f"\n\n---\n_Сказка сгенерирована: {now}_"

    async def generate_story_stream(
            self,
            request: StoryRequest
    ) -> AsyncGenerator[str, None]:
        """Generates a story stream from OpenAI.

        Args:
            request: The validated request object.

        Yields:
            str: Chunks of the generated text in Markdown format.

        Raises:
            HTTPException: If an error occurs during API communication.
        """
        prompt = self._construct_prompt(request)

        # Yield metadata first
        yield self._generate_metadata_header(request)

        try:
            stream = await self._client.chat.completions.create(
                model=self._settings.OPENAI_MODEL,
                messages=[
                    {"role": "system", "content": "Ты профессиональный детский писатель."},
                    {"role": "user", "content": prompt}
                ],
                max_completion_tokens=self._settings.OPENAI_MAX_TOKENS,
                temperature=self._settings.OPENAI_TEMPERATURE,
                stream=True
            )

            async for chunk in stream:
                if chunk.choices[0].delta.content is not None:
                    yield chunk.choices[0].delta.content

        except APIConnectionError as e:
            logger.error(f"OpenAI Connection Error: {e}")
            raise HTTPException(
                status_code=status.HTTP_503_SERVICE_UNAVAILABLE,
                detail="Could not connect to AI service."
            )
        except RateLimitError as e:
            logger.error(f"OpenAI Rate Limit Error: {e}")
            raise HTTPException(
                status_code=status.HTTP_429_TOO_MANY_REQUESTS,
                detail="AI service is currently overloaded."
            )
        except APIError as e:
            logger.error(f"OpenAI API Error: {e}")
            raise HTTPException(
                status_code=status.HTTP_502_BAD_GATEWAY,
                detail="AI service returned an error."
            )
        except Exception as e:
            logger.exception(f"Unexpected error during generation: {e}")
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail="Internal server error during story generation."
            )

        # Yield footer at the end
        yield self._generate_footer()