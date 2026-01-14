import sys
import json
import argparse
import logging
import pandas as pd
from pathlib import Path
from MLService import MLService


if __name__ == "__main__":

    results = MLService.evaluate_models(test_size=0.2)
    for target, result in results.items():
        print(f"\n{'='*50}")
        print(f"Оценка для: {target}")
        print('='*50)

        print(f"Точность на тесте: {result['test_accuracy']:.4f}")
        print(f"Точность в %: {result['test_accuracy'] * 100:.2f}%")

        # Смотрим отчет по классам
        report = result['classification_report']

        print("\nПо классам:")
        for class_name, metrics in report.items():
            if isinstance(metrics, dict):  # это метрики класса
                print(f"{class_name}:")
                print(f"  Точность: {metrics.get('precision', 0):.3f}")
                print(f"  Полнота: {metrics.get('recall', 0):.3f}")
                print(f"  F1-score: {metrics.get('f1-score', 0):.3f}")
                print(f"  Поддержка: {metrics.get('support', 0)}")

        # Общие метрики
        if 'accuracy' in report:
            print(f"\nОбщая точность: {report['accuracy']:.4f}")
