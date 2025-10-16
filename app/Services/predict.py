import sys
import json
import argparse
from pathlib import Path
from MLService import MLService
import warnings
warnings.filterwarnings('ignore')


def main():

    try:
        parser = argparse.ArgumentParser(description='Predict style and chess structure')
        parser.add_argument('--model_dir', required=True, help='Path to model directory')
        parser.add_argument('--input', required=True, help='Path to input JSON file')
        args = parser.parse_args()


        # Загрузка входных данных
        with open(args.input) as f:
            features = json.load(f)


        result = MLService.predict(features, args.model_dir)

        print(json.dumps(result, ensure_ascii=False))

    except Exception as e:
        print(json.dumps({"error": str(e)}))
        sys.exit(1)

if __name__ == "__main__":
    main()
