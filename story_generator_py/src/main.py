"""Main entry point for the FastAPI application.

Configures middleware, routers, logging, and global exception handlers.
"""

import logging.config
import time

from fastapi import FastAPI, Request, status
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse

from src.config import settings
from src.logging_config import get_logging_config
from src.story.router import router as story_router


# Setup Logging
logging.config.dictConfig(get_logging_config("DEBUG" if settings.DEBUG else "INFO"))
logger = logging.getLogger(__name__)


def create_app() -> FastAPI:
    """Factory function to create and configure the FastAPI app.

    Returns:
        FastAPI: The configured application instance.
    """
    app = FastAPI(
        title=settings.APP_TITLE,
        version=settings.APP_VERSION,
        docs_url="/docs" if settings.DEBUG else None,
        redoc_url="/redoc" if settings.DEBUG else None,
        openapi_url="/openapi.json" if settings.DEBUG else None,
    )

    app.add_middleware(
        CORSMiddleware,
        allow_origins=settings.CORS_ORIGINS,
        allow_credentials=settings.CORS_ALLOW_CREDENTIALS,
        allow_methods=settings.CORS_ALLOW_METHODS,
        allow_headers=settings.CORS_ALLOW_HEADERS,
    )

    @app.middleware("http")
    async def add_process_time_header(request: Request, call_next):
        """Middleware to log request processing time."""
        start_time = time.time()
        response = await call_next(request)
        process_time = time.time() - start_time

        # Log request details
        logger.info(
            "Request processed",
            extra={
                "method": request.method,
                "path": request.url.path,
                "status_code": response.status_code,
                "duration": round(process_time, 4)
            }
        )

        response.headers["X-Process-Time"] = str(process_time)
        return response

    @app.exception_handler(Exception)
    async def global_exception_handler(request: Request, exc: Exception):
        """Global handler for unhandled exceptions."""
        logger.error(f"Unhandled exception: {exc}", exc_info=True)
        return JSONResponse(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            content={"detail": "Internal Server Error. Please contact support."},
        )

    app.include_router(story_router)

    return app


app = create_app()