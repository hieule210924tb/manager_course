Hệ thống quản lý khóa học

Users:
-id : int primary key
-fullname : varchar(200)
-email: varchar(100)
-phone: varchar(50)
-address: varchar(500)
-forget_token: varchar(500)
-active_token: varchar(500)
-status: int (1: đã kích hoạt, 0: chưa kích hoạt)
-permission: text -> id khóa học
-group_id: int -> bảng groups
-created_at : datetime
-updated_at: datetime

Token_login:
-id: int primary key
-user_id INT -> bảng user
-token varchar(200)
-created_at : datetime
-updated_at: datetime

Course:
-id: int primary key
-name: varchar(100)
-slug: varchar(100) // link đường dẫn khóa học
-category_id: int -> bảng Course_category
-description: text
-price: int
-thumbnail varchar(200)
-created_at : datetime
-updated_at: datetime

Course_category:
-id: int primary key
-name varchar(100)
-slug varchar(100)
-created_at : datetime
-updated_at: datetime

Permission (Phân quyền):

Groups:
-id: int primary key
-name varchar(100)
-created_at : datetime
-updated_at: datetime
