"""两的玩家，游戏开始先输入名字
-用字典保存每个玩家的的信息：姓名，获胜次数
-电脑随机产生两个数，每个玩家轮流猜一个数"""

import random
import math


player_list = []
for player_num in range(1, 3):
    player_dist = {}
    player_dist['name'] = input('请输入玩家' + str(player_num) + '的名字\n')  # 输入名字
    player_dist['win_count'] = 0
    player_list.append(player_dist)  # 用字典保存每个玩家的的信息：姓名，获胜次数

while True:
    random_num_result = 0  # 电脑随机产生两个数的和
    for v in range(1, 3):
        random_num_result += random.randint(1, 9)
    print('随机数产生，请玩家猜一个数字\n')

    # 每个玩家个猜一个数，谁更接近随机数之和就胜出
    for player in player_list:
        player['guess_num'] = int(input('玩家' + player['name'] + '猜的数是:\n'))
        player['num_abs'] = math.fabs(random_num_result - player['guess_num'])

    if player_list[0]['num_abs'] == player_list[1]['num_abs']:
        print('平局')
    elif player_list[0]['num_abs'] > player_list[1]['num_abs']:
        print('玩家' + player_list[1]['name'] + '赢了,随机数之和是:' + str(random_num_result) + ',玩家' + player_list[1][
            'name'] + '猜的数是:' + str(player_list[1]['guess_num']) + ',玩家' + player_list[0]['name'] + '猜的数是:' + str(
            player_list[0]['guess_num']))
        player_list[1]['win_count'] += 1
    else:
        print('玩家' + player_list[0]['name'] + '赢了,随机数之和是:' + str(random_num_result) + ',玩家' + player_list[0][
            'name'] + '猜的数是:' + str(player_list[0]['guess_num']) + ',玩家' + player_list[1]['name'] + '猜的数是:' + str(
            player_list[1]['guess_num']))
        player_list[0]['win_count'] += 1

    is_end = input('游戏是否继续(yes/no)\n')
    if is_end == 'no' or is_end == 'n':
        print('最终结果:\n')
        print('玩家'+player_list[0]['name']+'赢了'+str(player_list[0]['win_count'])+'局')
        print('玩家'+player_list[1]['name']+'赢了'+str(player_list[1]['win_count'])+'局')
        break
