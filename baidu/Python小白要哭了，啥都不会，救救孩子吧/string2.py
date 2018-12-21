"""输入一个字符串,分别统计大写,小写数字以及其他字符的个数并通过格式化方式输出"""

input_str = input('please string:')

upper = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V',
         'W', 'X', 'Y', 'Z']
lower = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
         'w', 'x', 'y', 'z']
num = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9']

upper_count = lower_count = num_count = other_count = 0

for s in list(input_str):
    if s in upper:
        upper_count += 1
    elif s in lower:
        lower_count += 1
    elif s in num:
        num_count += 1
    else:
        other_count += 1

print('大写字母' + str(upper_count) + '个;小写字母' + str(lower_count) + '个;数字' + str(num_count) + '个;其他字符' + str(
    other_count)+'个')
