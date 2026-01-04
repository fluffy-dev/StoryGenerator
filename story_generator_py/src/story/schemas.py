from typing import List, Literal

from pydantic import BaseModel, Field


class StoryRequest(BaseModel):
    """Request schema for story generation.

    Attributes:
        age: The age of the child. Must be greater than 0.
        language: The language code ('ru' or 'kk').
        characters: A list of character names. Must contain at least one character.
    """

    age: int = Field(
        ...,
        gt=0,
        description="Age of the child. Must be greater than 0."
    )
    language: Literal["ru", "kk"] = Field(
        ...,
        description="Language code: 'ru' for Russian, 'kk' for Kazakh."
    )
    characters: List[str] = Field(
        ...,
        min_length=1,
        description="List of characters. Must contain at least one element."
    )

    model_config = {
        "json_schema_extra": {
            "example": {
                "age": 6,
                "language": "kk",
                "characters": ["Алдар Көсе", "Әйел Арстан"]
            }
        }
    }