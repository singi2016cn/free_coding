"""
Python子类继承，如果不用父类的函数和方法，应该怎么写
"""


class Car:

    def show_name(self):
        print('car name')


class EeleCar(Car):
    pass


car = EeleCar()
car.show_name()


class Car:
    def show_name(self):
        print('car name')


class EeleCar(Car):

    def class_1(self,ary1,ary2):
        pass
