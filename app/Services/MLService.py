import pandas as pd
import xgboost as xgb
from sklearn.preprocessing import LabelEncoder
import joblib
import numpy as np
from pathlib import Path
from sklearn.model_selection import cross_val_score, StratifiedKFold
from sklearn.metrics import accuracy_score, classification_report
from sklearn.decomposition import PCA
from sklearn.manifold import TSNE
from sklearn.model_selection import train_test_split
from sklearn.metrics import classification_report


class MLService:
    FEATURES = [
        'left_wall_color_var', 'left_wall_depth_grad', 'left_wall_lum_std',
        'ceiling_color_var', 'ceiling_depth_grad', 'ceiling_lum_std',
        'right_wall_color_var', 'right_wall_depth_grad', 'right_wall_lum_std',
        'floor_color_var', 'floor_depth_grad', 'floor_lum_std',
        'vertical_symmetry', 'horizontal_contrast', 'depth_contrast',
        'pattern_consistency'
    ]

    TARGETS = ['style_class', 'chess_structure']


    @staticmethod
    def evaluate_models(test_size=0.2, random_state=42):
        """Оценка точности моделей с разделением на train/test"""

        try:
            df = pd.read_csv('training_data.csv')

            missing = set(MLService.FEATURES + MLService.TARGETS) - set(df.columns)
            if missing:
                raise ValueError(f"Missing columns: {missing}")

            evaluation_results = {}

            for target in MLService.TARGETS:

                X = df[MLService.FEATURES]
                y = df[target]

                # Разделение на train/test
                X_train, X_test, y_train, y_test = train_test_split(
                    X, y, test_size=test_size, random_state=random_state, stratify=y
                )

                encoder = LabelEncoder()
                y_train_encoded = encoder.fit_transform(y_train)
                y_test_encoded = encoder.transform(y_test)

                model = xgb.XGBClassifier(max_depth=3,
    reg_alpha=0.1,learning_rate=0.05,
    n_estimators=10)
                model.fit(X_train, y_train_encoded)

                y_pred = model.predict(X_test)

                report = classification_report(
                    y_test_encoded, y_pred,
                    target_names=encoder.classes_,
                    output_dict=True
                )

                evaluation_results[target] = {
                    'test_accuracy': accuracy_score(y_test_encoded, y_pred),
                    'classification_report': report,
                    'model': model,
                    'encoder': encoder
                }


            return evaluation_results

        except Exception as e:
            raise
    @staticmethod
    def train_model():
        model_dir= Path(__file__).parent.absolute()
        """Обучение моделей по данным из training_data.csv"""

        try:
            model_path = Path(model_dir)
            model_path.mkdir(exist_ok=True)

            df = pd.read_csv('training_data.csv')

            missing = set(MLService.FEATURES + MLService.TARGETS) - set(df.columns)
            if missing:
                raise ValueError(f"Missing columns: {missing}")

            for target in MLService.TARGETS:

                X = df[MLService.FEATURES]
                y = df[target]

                encoder = LabelEncoder()
                y_encoded = encoder.fit_transform(y)

                model = xgb.XGBClassifier()
                model.fit(X, y_encoded)

                joblib.dump(model, model_path / f'model_{target}.pkl')
                joblib.dump(encoder, model_path / f'encoder_{target}.pkl')

            return True

        except Exception as e:
            raise

    @staticmethod
    def predict(features, model_dir='models'):
        model_dir= Path(__file__).parent.absolute()
        """Предсказание по готовым признакам"""

        try:
            model_path = Path(model_dir)
            if not model_path.exists():
                raise FileNotFoundError(f"Model directory not found: {model_dir}")


            missing_features = set(MLService.FEATURES) - set(features.keys())
            if missing_features:
                raise ValueError(f"Missing features: {missing_features}")


            input_data = [features[key] for key in MLService.FEATURES]

            results = {}
            for target in MLService.TARGETS:
                model = joblib.load(model_path / f'model_{target}.pkl')
                encoder = joblib.load(model_path / f'encoder_{target}.pkl')

                pred = model.predict([input_data])[0]
                results[target] = encoder.inverse_transform([pred])[0]

            return results

        except Exception as e:
            raise
