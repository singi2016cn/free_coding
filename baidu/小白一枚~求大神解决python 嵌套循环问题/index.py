numBlocks = 3
for block in range(1, numBlocks + 1):
    for line in range(1, block * 2):
        for star in range(1, (block + line) * 2):
            print('*', end='')
        print('')
    print('')
