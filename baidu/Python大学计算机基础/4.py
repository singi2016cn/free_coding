# 4 程序修改

# 4.1

x = 1
if x < 2:
    if x < 1:
        y = x + 1
else:
    y = x + 2

# 4.2

# 1) else匹配之前的缩进相同且最接近的if
# 2) 改之前是y=x+2先执行,之后是y=x+1先执行
