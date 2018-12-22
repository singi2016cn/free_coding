"""
学生成绩表
姓名  语文     数学  英语  总分
王敏  95.5    98
利用字典显示上表内容
"""

header = ['姓名', '语文', '数学', '英语', '总分', '平均分']
score = [
    {
        'name': '王敏',
        'Chinese': 95.5,
        'Math': 98,
        'English': 97,
    },
    {
        'name': '刘志坚',
        'Chinese': 96,
        'Math': 92,
        'English': 82,
    },
    {
        'name': '谢塞科',
        'Chinese': 91,
        'Math': 100,
        'English': 90,
    },
    {
        'name': '肖江秋',
        'Chinese': 88,
        'Math': 93,
        'English': 99,
    }
]

# 输出表格

print('学生成绩表')
blank = '\t\t\t'
little_blank = '\t\t'
Chinese_max = {'name': '', 'sorce': 0}  # 语文
Math_max = {'name': '', 'sorce': 0}  # 数学
English_max = {'name': '', 'sorce': 0}  # 英语

for v in header:
    print(v, end=blank)

for v in score:
    print()

    if Chinese_max['sorce'] == 0:
        Chinese_max['sorce'] = v['Chinese']
    else:
        # 对比分数
        if Chinese_max['sorce'] < v['Chinese']:
            Chinese_max['sorce'] = v['Chinese']
            Chinese_max['name'] = v['name']
    if Chinese_max['name'] == '':
        Chinese_max['name'] = v['name']

    if Math_max['sorce'] == 0:
        Math_max['sorce'] = v['Math']
    else:
        # 对比分数
        if Math_max['sorce'] < v['Math']:
            Math_max['sorce'] = v['Math']
            Math_max['name'] = v['name']
    if Math_max['name'] == '':
        Math_max['name'] = v['name']

    if English_max['sorce'] == 0:
        English_max['sorce'] = v['English']
    else:
        # 对比分数
        if English_max['sorce'] < v['English']:
            English_max['sorce'] = v['English']
            English_max['name'] = v['name']
    if English_max['name'] == '':
        English_max['name'] = v['name']

    print(v['name'], end='')
    if len(v['name']) > 2:
        print(end=little_blank)
    else:
        print(end=blank)

    print(v['Chinese'], end='')
    if '.' in str(v['Chinese']):
        print(end=little_blank)
    else:
        print(end=blank)

    print(v['Math'], end='')
    if '.' in str(v['Math']):
        print(end=little_blank)
    else:
        print(end=blank)

    print(v['English'], end='')
    if '.' in str(v['English']):
        print(end=little_blank)
    else:
        print(end=blank)

    total = v['Chinese'] + v['Math'] + v['English']
    print(total, end='')
    if '.' in str(total):
        print(end=little_blank)
    else:
        print(end=blank)

    print(round(total / 3), end='')  # 平均分

# 每科最高分

print('\n\n最高分')
print('语文\t\t' + Chinese_max['name'] + '\t' + str(Chinese_max['sorce']))
print('数学\t\t' + Math_max['name'] + '\t' + str(Math_max['sorce']))
print('英语\t\t' + English_max['name'] + '\t' + str(English_max['sorce']))
