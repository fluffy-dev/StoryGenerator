from typing import Any, Dict


def get_logging_config(log_level: str = "INFO") -> Dict[str, Any]:
    """Returns the logging configuration dictionary.

    Args:
        log_level: The logging level (DEBUG, INFO, WARNING, ERROR).

    Returns:
        Dict: Logging configuration dict compatible with logging.config.dictConfig.
    """
    return {
        "version": 1,
        "disable_existing_loggers": False,
        "formatters": {
            "json": {
                "()": "pythonjsonlogger.jsonlogger.JsonFormatter",
                "format": "%(asctime)s %(levelname)s %(name)s %(message)s",
            },
            "standard": {
                "format": "%(asctime)s [%(levelname)s] %(name)s: %(message)s"
            },
        },
        "handlers": {
            "default": {
                "class": "logging.StreamHandler",
                "formatter": "json",
                "stream": "ext://sys.stdout",
            },
        },
        "loggers": {
            "root": {
                "handlers": ["default"],
                "level": log_level,
                "propagate": False
            },
            "uvicorn": {
                "handlers": ["default"],
                "level": "INFO",
                "propagate": False
            },
            "src": {
                "handlers": ["default"],
                "level": log_level,
                "propagate": False
            },
        },
    }