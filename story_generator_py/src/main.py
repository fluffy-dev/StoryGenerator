import logging

from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware

from src.config import Settings as project_settings
from src.story.router import router as story_router

# Configure logging
logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(name)s: %(message)s"
)


def create_app() -> FastAPI:
    """Factory function to create and configure the FastAPI app.

    Returns:
        FastAPI: The configured application instance.
    """

    app = FastAPI(
        title=project_settings.APP_TITLE,
        version=project_settings.APP_VERSION,
        docs_url="/docs" if project_settings.DEBUG else None,
        redoc_url="/redoc" if project_settings.DEBUG else None,
        openapi_url="/openapi.json" if project_settings.DEBUG else None,
    )

    # Configure CORS
    app.add_middleware(
        CORSMiddleware,
        allow_origins=project_settings.CORS_ORIGINS,
        allow_credentials=project_settings.CORS_ALLOW_CREDENTIALS,
        allow_methods=project_settings.CORS_ALLOW_METHODS,
        allow_headers=project_settings.CORS_ALLOW_HEADERS,
    )

    # Include Routers
    app.include_router(story_router)

    return app


app = create_app()