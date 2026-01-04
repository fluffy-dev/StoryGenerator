from typing import Annotated
from fastapi import Depends

from src.story.service import StoryService


IStoryService: type[StoryService] = Annotated[StoryService, Depends()]