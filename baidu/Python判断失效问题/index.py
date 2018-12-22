"""
这个运行以后没有输出，程序直接结束
但是如果条件判断改成 f!=n 就会有输出
"""

n = 100
for g in range(1, n // 5):
    for m in range(1, n // 3 - g):
        for c in range(1, n - m * 3 - g * 5):
            f = g * 5 + m * 3 + c
            print(str(f)+',',end='')
            if f == n:
                print(g, m, c * 3)
