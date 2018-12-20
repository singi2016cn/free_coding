class Course:
    """课程"""

    def __init__(self, course_name, course_id, credit, instructor_name, address, description):
        self.__course_name = course_name
        self.__course_id = course_id
        self.__credit = credit
        self.__instructor_name = instructor_name
        self.__address = address
        self.__description = description

    def get_course_name(self):
        return self.__course_name

    def __str__(self):
        string = 'Course_name:' + self.__course_name + '\n'
        string += 'Course_id:' + self.__course_id + '\n'
        string += 'Credit:' + self.__credit + '\n'
        string += 'Instructor_name:' + self.__instructor_name + '\n'
        string += 'Address:' + self.__address + '\n'
        string += 'Description:' + self.__description + '\n'
        return string


if __name__ == '__main__':
    fi = open('course.txt', 'r')
    course_list = []  # 课程对象列表
    while True:
        line1 = fi.readline().strip()  # 读取1行
        if line1 == '':
            break
        line2 = fi.readline().strip()  # 读取1行
        tem_list = line1.split(sep=",")
        course_list.append(
            Course(tem_list[0], tem_list[1], tem_list[2], tem_list[3], tem_list[4], line2))  # 将实例化的课程对象添加到课程对象列表中
    fi.close()

    # 打印保存的课程对象数组
    for course in course_list:
        print(course)
