"""输入一个字符串,将该字符串下标为偶数的字符组成新串并通过格式化方式输出"""


input_str = input('please string:')
print(''.join(list(input_str)[1::2]))
