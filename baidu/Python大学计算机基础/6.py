"""
    5
   45
  345
 2345
12345
"""

n = 5
while n >= 1:
    i = 1
    while i <= 5:
        if i >= n:
            print(i, end='')
        else:
            print(' ', end='')
        i += 1
    print('')
    n -= 1

"""
ABCDE
 BCDE
  CDE
   DE
    E
"""

dist = {
    1: 'A',
    2: 'B',
    3: 'C',
    4: 'D',
    5: 'E'
}
n = 5
while n >= 1:
    i = 1
    while i <= 5:
        if i >= 6 - n:
            print(dist[i], end='')
        else:
            print(' ', end='')
        i += 1
    print('')
    n -= 1
