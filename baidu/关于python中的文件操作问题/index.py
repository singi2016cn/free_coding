"""
我现在有一张列表，列表里边是若干个字典，这个列表可以作为参数给到一个函数里，打印出一张表格。
现在我把列表写到一个txt文件中，当然保存到文件中的是字符串形式，我再重新读取这个文件中的内容时，
之前的列表只能以字符串的形式取出，请问怎么把这个字符串再转换为之前的列表来使用？
"""

import json


def print_list(my_list):
    print(my_list)


my_list = [
    {
        'name': 'singi'
    },
    {
        'name': 'sunjun'
    }
]

file_name = 'my_list.json'

# 写入文件
with open(file_name, 'w') as f:
    json.dump(my_list, f)

# 读取文件
with open(file_name) as f:
    my_list_load = json.load(f)
    print_list(my_list_load)
