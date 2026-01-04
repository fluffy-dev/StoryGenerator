from typing import List

from pydantic import Field
from pydantic_settings import BaseSettings, SettingsConfigDict


class Settings(BaseSettings):
    """Global application settings."""

    # App Config
    APP_TITLE: str = Field("Story Generator")
    APP_VERSION: str = Field("1.0.0")
    DEBUG: bool = Field(False)

    # OpenAI Config
    OPENAI_API_KEY: str = Field(..., description="API Key for OpenAI")
    OPENAI_MODEL: str = "gpt-3.5-turbo"
    OPENAI_MAX_TOKENS: int = 1500
    OPENAI_TEMPERATURE: float = 0.7

    # CORS Config
    CORS_ORIGINS: List[str] = ["*"]
    CORS_ALLOW_CREDENTIALS: bool = True
    CORS_ALLOW_METHODS: List[str] = ["*"]
    CORS_ALLOW_HEADERS: List[str] = ["*"]


settings = Settings()