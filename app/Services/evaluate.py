import sys
import json
import argparse
import logging
import pandas as pd
from pathlib import Path
from MLService import MLService


if __name__ == "__main__":
    
    results = MLService.evaluate_models(test_size=0.3)