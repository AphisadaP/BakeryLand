import mysql.connector
import json

connection = mysql.connector.connect(
    host="localhost",
    user="Wstd14",
    password="hy3fBxRi",
    database="sec1_14"
)

cursor = connection.cursor()

# สร้างคำสั่ง SQL เพื่อดึงข้อมูล Item ที่ถูกสั่งซื้อทั้งหมด
query = """
    SELECT 
        Item.ItemID,
        Item.ItemName,
        Item.BasePrice AS Price,
        Item.ItemIMG,
        SUM(OrderItem.Quantity) AS TotalQuantityOrdered
    FROM 
        Item
    JOIN 
        OrderItem ON Item.ItemID = OrderItem.ItemID
    GROUP BY 
        Item.ItemID, Item.ItemName, Item.BasePrice, Item.ItemIMG
"""

# รัน query และดึงข้อมูล
cursor.execute(query)
items = cursor.fetchall()

# จัดรูปแบบข้อมูลให้อยู่ในรูปแบบ JSON
item_list = [
    {
        "ItemID": item[0],
        "ItemName": item[1],
        "Price": item[2],
        "TotalQuantityOrdered": item[4],
        "ItemIMG": item[3]
    }
    for item in items
]

with open('order.json', 'w', encoding='utf-8') as json_file:
    json.dump(item_list, json_file, ensure_ascii=False, indent=4)

cursor.close()
connection.close()

print("Data has been written to ordered_items.json")



