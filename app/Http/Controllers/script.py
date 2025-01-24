from transformers import GPT2Tokenizer, GPT2LMHeadModel
import torch

# Инициализация токенизатора и модели
tokenizer = GPT2Tokenizer.from_pretrained("gpt2")
model = GPT2LMHeadModel.from_pretrained("gpt2")

# Текст для генерации
input_text = "Привет, расскажи о себе"

# Токенизация текста
inputs = tokenizer.encode(input_text, return_tensors="pt")

# Генерация текста
output = model.generate(inputs, max_length=50, num_return_sequences=1)

# Декодирование сгенерированных токенов
output_text = tokenizer.decode(output[0], skip_special_tokens=True)
print(output_text)