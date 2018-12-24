import random


class Classes:

    def __init__(self, class_name):
        self.class_name = class_name
        self.students = []

    def add_student(self, student):
        self.students.append(student)


class Person:

    def __init__(self, name):
        self.name = name


class Teacher(Person):

    def __init__(self, name, tid):
        super().__init__(name)
        self.tid = tid

    def teaching(self, classes):
        print('%s老师在给%s班上同学上课' % (self.name, classes.class_name))
        for i in range(len(classes.students)):
            classes.students[i].learn_start()

    def set_grade(self, classes):
        print('%s老师在打分' % self.name)
        for i in range(len(classes.students)):
            if classes.students[i].state == 1:
                classes.students[i].set_score(100)
            elif classes.students[i].state == 2:
                classes.students[i].set_score(60)
            else:
                classes.students[i].set_score(0)


class Student(Person):

    def __init__(self, name, sid):
        super().__init__(name)
        self.sid = sid

    def learn_start(self):
        self.state = random.randint(1, 4)
        if self.state == 1:
            print('%s在学习！' % self.name)
        elif self.state == 2:
            print('%s在开小差~' % self.name)
        else:
            print('%s逃课了~' % self.name)

    def set_score(self, score):
        self.score = score
        print('%s的成绩是%d' % (self.name, self.score))


c1 = Classes('网络1701')
s1 = Student('张三', '01')
s2 = Student('李四', '02')
s3 = Student('王五', '03')
c1.add_student(s1)
c1.add_student(s2)
c1.add_student(s3)
t1 = Teacher('老六', '001')
t1.teaching(c1)
t1.set_grade(c1)
