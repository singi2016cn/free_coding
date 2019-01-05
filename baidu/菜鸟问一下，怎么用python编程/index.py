"""
菜鸟问一下，怎么用python编程 1/0！+1/1！+1/2！+.....+1/10000！
"""

import math

result = 0
for i in range(0, 10001):
    result += 1/math.factorial(i)

print(result)
