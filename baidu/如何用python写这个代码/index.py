"""
Problem 3: Populating the ratings tableThe function populateratings
( will read in a list of movie names, critic namesand ratings, and populate a two-dimensional array.
This is the same asroblem 5 in Assignment 4, except that the movie and critic names are inputthis time
instead of the index numbers for the movie and critic
问题3：填充评级表函数populate ratings
（将读取电影名称、评论家名称和评级列表，并填充一个二维数组）。
这是作业4中同样的问题5，只是这次输入的是电影和评论家的名字，
而不是电影和评论家的索引号。
"""


def populateratings():
    result_list = []

    while True:
        input_list = []
        movies_name = input("请输入电影家名字:\n")
        input_list.append(movies_name)
        critic_name = input("请输入评论家名字:\n")
        input_list.append(critic_name)
        ratings_name = input("请输入评级:\n")
        input_list.append(ratings_name)
        # 添加到二维数组
        result_list.append(input_list)

        is_done = input('继续输入吗?(Y:继续,N结束)\n')
        if is_done.lower() == 'n':
            break

    return result_list


result = populateratings()
print(result)
