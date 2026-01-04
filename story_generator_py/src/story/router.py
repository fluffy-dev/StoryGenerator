from typing import Annotated

from fastapi import APIRouter, Depends
from fastapi.responses import StreamingResponse

from src.story.schemas import StoryRequest
from src.story.service import StoryService
from src.story.dependencies import IStoryService

router = APIRouter(prefix="/story", tags=["Story Generator"])


@router.post(
    "/generate",
    summary="Generate a fairy tale",
    description="Generates a streaming markdown story based on age, language, and characters.",
    response_class=StreamingResponse
)
async def generate_story(
    request: StoryRequest,
    service: IStoryService
) -> StreamingResponse:
    """Endpoint to generate a story.

    Args:
        request: Validation Input DTO.
        service: Injected business logic service.

    Returns:
        StreamingResponse: A stream of text/markdown data.
    """
    return StreamingResponse(
        service.generate_story_stream(request),
        media_type="text/markdown"
    )