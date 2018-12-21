"""输入一个字符串,然后再输入字符串中的两个位置,屏幕输出其长度,取出位置之间的子串并通过格式化方式输出"""

input_str = input('请输入一个字符串:')
input_str_position = input('请输入字符串中的两个位置:').strip().split(',')

str_len = len(input_str)
sub_str = input_str[int(input_str_position[0]):int(input_str_position[1])]

print('长度为:'+str(str_len)+',子串为:'+sub_str)



