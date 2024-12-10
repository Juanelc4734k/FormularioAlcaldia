import pandas as pd
import os
import openpyxl

class ExcelReader:
    def __init__(self, file_path):
        self.file_path = os.path.abspath(file_path)

    def get_headers(self):
        df = pd.read_excel(self.file_path)
        headers = df.columns.tolist()
        return headers
    
if __name__ == "__main__":
    excel_reader = ExcelReader("../Guias/REGISTRO DE TRAMITES PERSONERÍA 2024.xlsx")
    headers = excel_reader.get_headers()
    print("Headers del excel: ", headers)
