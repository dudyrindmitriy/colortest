# import sys
# import json
# import argparse
# import logging
# import os
# import traceback
# from MLService import MLService
# from pathlib import Path

# LOG_PATH = Path(__file__).parent / 'python_errors.log'

# logging.basicConfig(
#     level=logging.DEBUG,
#     format='%(asctime)s - %(levelname)s - %(message)s',
#     handlers=[
#         logging.FileHandler(LOG_PATH),
#         logging.StreamHandler()
#     ]
# )
# def main():
#     logger = logging.getLogger('predict.main')
#     try:
#         MLService.train_model()
#         # Парсинг аргументов должен быть ПЕРВЫМ
#         parser = argparse.ArgumentParser(description='Predict industry and chess structure')
#         parser.add_argument('--industry_model', required=True, help='Path to industry model')
#         parser.add_argument('--chess_model', required=True, help='Path to chess model')
#         parser.add_argument('--input', required=True, help='Path to input data file')
#         args = parser.parse_args()

#         logger.info("Script started")
#         logger.info(f"Received arguments: {sys.argv}")

#         # Проверка файлов ПОСЛЕ парсинга аргументов
#         required_files = {
#             'Industry model': args.industry_model,
#             'Chess model': args.chess_model,
#             'Input file': args.input
#         }

#         for name, path in required_files.items():
#             if not os.path.exists(path):
#                 raise FileNotFoundError(f"{name} not found: {path}")
#             logger.info(f"{name} found: {path}")

#         # Чтение входных данных
#         try:
#             with open(args.input, 'r') as f:
#                 features = json.load(f)
#                 logger.info("Successfully loaded input data")
#         except json.JSONDecodeError as e:
#             raise ValueError(f"Invalid JSON format in input file: {e}")

#         logger.debug(f"Features received: {features}")

#         # Выполнение предсказания
#         try:
#             industry, chess = MLService.predict(
#                 features,
#                 args.industry_model,
#                 args.chess_model
#             )
#             logger.info("Prediction completed successfully")
#         except Exception as e:
#             raise RuntimeError(f"Prediction failed: {str(e)}")

#         # Формирование результата
#         result = {
#             'industry': str(industry),
#             'chess_structure': str(chess)
#         }

#         print(json.dumps(result, ensure_ascii=False))
#         return 0

#     except Exception as e:
#         error_response = {
#             'error': str(e),
#             'trace': traceback.format_exc()
#         }
#         print(json.dumps(error_response))  # Всегда возвращаем JSON
#         sys.exit(1)
# if __name__ == "__main__":
#     main()
import sys
import json
import argparse
import logging
from pathlib import Path
from MLService import MLService

def configure_logging():
    logging.basicConfig(
        level=logging.INFO,
        format='%(asctime)s - %(levelname)s - %(message)s',
        handlers=[
            logging.FileHandler('predict.log'),
            logging.StreamHandler()
        ]
    )

def main():
    configure_logging()
    logger = logging.getLogger('predict')
    
    try:
        parser = argparse.ArgumentParser(description='Predict style and chess structure')
        parser.add_argument('--model_dir', required=True, help='Path to model directory')
        parser.add_argument('--input', required=True, help='Path to input JSON file')
        args = parser.parse_args()

        logger.info("Starting prediction process")
        
        # Загрузка входных данных
        with open(args.input) as f:
            features = json.load(f)

        logger.debug("Loaded features: %s", features)
        
        # Выполнение предсказания
        result = MLService.predict(features, args.model_dir)
        
        # Формирование результата
        logger.info("Prediction result: %s", result)
        print(json.dumps(result, ensure_ascii=False))

    except Exception as e:
        logger.error("Prediction failed: %s", str(e))
        print(json.dumps({"error": str(e)}))
        sys.exit(1)

if __name__ == "__main__":
    main()