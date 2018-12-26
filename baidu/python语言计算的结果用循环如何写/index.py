"""python语言计算2**100+3**101+······+10**108的结果用循环如何写"""


result = 0
limit = 98
for num in range(2, 11):
    index = num+limit
    result2 = num**index
    result = result + result2

print(result)
