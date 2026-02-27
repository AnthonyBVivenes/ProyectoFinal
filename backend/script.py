import pymysql
import os
from datetime import datetime

# ariables de entorno
DB_HOST = os.getenv('DB_HOST', 'db')
DB_USER = os.getenv('MYSQL_USER', 'admin')
DB_PASSWORD = os.getenv('MYSQL_PASSWORD', 'admin123')
DB_NAME = os.getenv('MYSQL_DATABASE', 'campus')

def generarReporte():
    try:
        conn = pymysql.connect(
            host=DB_HOST,
            user=DB_USER,
            password=DB_PASSWORD,
            database=DB_NAME
        )
        cursor = conn.cursor()
        
        cursor.callproc('sp_estadisticas_visitantes')
        resultado = cursor.fetchone()
        
        total = resultado[0] if resultado else 0
        
        contenido = f"""
Fecha: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}
Total visitantes: {total}
"""
        with open('/app/reportes/reporte.txt', 'w') as f:
            f.write(contenido)
        
        cursor.close()
        conn.close()
        
    except Exception as e:
        print(f"Error:{e}")

generarReporte()