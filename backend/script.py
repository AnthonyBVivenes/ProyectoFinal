import pymysql
import os
import time
from datetime import datetime

DB_HOST = os.getenv('DB_HOST')
DB_USER = os.getenv('MYSQL_USER')
DB_PASSWORD = os.getenv('MYSQL_PASSWORD')
DB_NAME = os.getenv('MYSQL_DATABASE')

def generarReporte():

    for intento in range(10):
        try:
            print(f"Intento {intento + 1}/10...")
            
            conn = pymysql.connect(
                host=DB_HOST,
                user=DB_USER,
                password=DB_PASSWORD,
                database=DB_NAME,
                connect_timeout=5
            )
            print("Conectado")
            cursor = conn.cursor()

            #Cambiar esto a un proc sql para amyor formalidad
            cursor.execute("SELECT COUNT(*) FROM visitantes")
            total = cursor.fetchone()[0]
            
            cursor.execute("SELECT COUNT(*) FROM visitantes WHERE salida_registrada = FALSE")
            dentro = cursor.fetchone()[0]

            cursor.execute("SELECT COUNT(*) FROM visitantes WHERE salida_registrada = TRUE")
            fuera = cursor.fetchone()[0]
            

            contenido = f"""
Fecha: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}
Total visitantes registrados: {total}
Actualmente dentro: {dentro}
"""
            
            with open('/app/reportes/reporte.txt', 'w', encoding='utf-8') as f:
                f.write(contenido)

            cursor.close()
            conn.close()
            print("Reporte generado")
            print(contenido)
            return

        except pymysql.Error as e:
            print(f"Error en la bdd: {e}")
        except Exception as e:
            print(f"Error en el backend: {e}")
            import traceback
            traceback.print_exc()
        
        if intento < 9:
            print("Reintentando")
            time.sleep(3)

generarReporte()