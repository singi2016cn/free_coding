"""输出100内的素数"""


numbers = list(range(2, 101))


def is_not_prime(num):
    for v in range(2, num):
        if num % v == 0:
            return True


for v in numbers:
    if is_not_prime(v):
        numbers.remove(v)

print(numbers)





