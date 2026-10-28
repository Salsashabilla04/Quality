# -*- coding: utf-8 -*-
"""
Script Generator Lengkap Dokumen Word (.docx) Perhitungan Apriori KDD
Sistem Monitoring Customer Complaint & NCR (PT Wahana Bermuda Nusantara)
Peneliti: Salsa Shabilla
"""

import os
import docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import nsdecls, qn

def set_cell_background(cell, hex_color):
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{hex_color}"/>')
    tcPr.append(shd)

def set_cell_margins(cell, top=100, bottom=100, left=150, right=150):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = parse_xml(f'''
        <w:tcMar {nsdecls("w")}>
            <w:top w:w="{top}" w:type="dxa"/>
            <w:bottom w:w="{bottom}" w:type="dxa"/>
            <w:left w:w="{left}" w:type="dxa"/>
            <w:right w:w="{right}" w:type="dxa"/>
        </w:tcMar>
    ''')
    tcPr.append(tcMar)

def set_table_borders(table, color="CBD5E1", sz="4", val="single"):
    tblPr = table._tbl.tblPr
    borders = parse_xml(f'''
        <w:tblBorders {nsdecls("w")}>
            <w:top w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>
            <w:bottom w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>
            <w:insideH w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>
            <w:insideV w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>
            <w:left w:val="none"/>
            <w:right w:val="none"/>
        </w:tblBorders>
    ''')
    tblPr.append(borders)

def format_table(table, col_widths, headers, data_rows, header_bg="1A365D", alt_bg="F8FAFC"):
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    set_table_borders(table, color="CBD5E1", sz="4")
    
    # Header row
    hdr_cells = table.rows[0].cells
    for i, title in enumerate(headers):
        hdr_cells[i].text = title
        set_cell_background(hdr_cells[i], header_bg)
        set_cell_margins(hdr_cells[i], top=140, bottom=140, left=150, right=150)
        p = hdr_cells[i].paragraphs[0]
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        for run in p.runs:
            run.font.name = "Times New Roman"
            run.font.size = Pt(10)
            run.font.bold = True
            run.font.color.rgb = RGBColor(255, 255, 255)
            
    # Data rows
    for r_idx, row_data in enumerate(data_rows):
        row_cells = table.rows[r_idx + 1].cells
        bg = alt_bg if r_idx % 2 == 1 else "FFFFFF"
        for c_idx, val in enumerate(row_data):
            row_cells[c_idx].text = str(val)
            set_cell_background(row_cells[c_idx], bg)
            set_cell_margins(row_cells[c_idx], top=100, bottom=100, left=140, right=140)
            p = row_cells[c_idx].paragraphs[0]
            val_str = str(val).strip()
            if (val_str.endswith('%') or 
                val_str.replace('.', '', 1).isdigit() or 
                val_str in ['Memenuhi', 'Tidak Memenuhi', 'Prioritas', 'Dipertimbangkan', 'Dipantau', 
                            'Sangat Kuat', 'Kuat', 'Sedang', 'Lemah', 'Close', 'Open', 'Ya', 'Tidak',
                            '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15',
                            '16', '17', '18', '19', '20', '21', '22', '23', '24', '25']):
                p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            else:
                p.alignment = WD_ALIGN_PARAGRAPH.LEFT
            for run in p.runs:
                run.font.name = "Times New Roman"
                run.font.size = Pt(9.5)
                run.font.color.rgb = RGBColor(45, 55, 72)
                
    # Widths
    for row in table.rows:
        for idx, width in enumerate(col_widths):
            row.cells[idx].width = Inches(width)

def add_heading_1(doc, text):
    h = doc.add_paragraph()
    h.paragraph_format.space_before = Pt(18)
    h.paragraph_format.space_after = Pt(8)
    h.paragraph_format.keep_with_next = True
    run = h.add_run(text)
    run.font.name = "Times New Roman"
    run.font.size = Pt(14)
    run.font.bold = True
    run.font.color.rgb = RGBColor(26, 54, 93)
    return h

def add_heading_2(doc, text):
    h = doc.add_paragraph()
    h.paragraph_format.space_before = Pt(14)
    h.paragraph_format.space_after = Pt(6)
    h.paragraph_format.keep_with_next = True
    run = h.add_run(text)
    run.font.name = "Times New Roman"
    run.font.size = Pt(12)
    run.font.bold = True
    run.font.color.rgb = RGBColor(43, 108, 176)
    return h

def add_heading_3(doc, text):
    h = doc.add_paragraph()
    h.paragraph_format.space_before = Pt(10)
    h.paragraph_format.space_after = Pt(4)
    h.paragraph_format.keep_with_next = True
    run = h.add_run(text)
    run.font.name = "Times New Roman"
    run.font.size = Pt(11)
    run.font.bold = True
    run.font.color.rgb = RGBColor(45, 55, 72)
    return h

def add_body_p(doc, text, bold_prefix=None, italic=False, align=WD_ALIGN_PARAGRAPH.JUSTIFY):
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(0)
    p.paragraph_format.space_after = Pt(6)
    p.paragraph_format.line_spacing = 1.15
    p.alignment = align
    if bold_prefix:
        r_pre = p.add_run(bold_prefix)
        r_pre.font.name = "Times New Roman"
        r_pre.font.size = Pt(11)
        r_pre.font.bold = True
        r_pre.font.color.rgb = RGBColor(26, 54, 93)
    run = p.add_run(text)
    run.font.name = "Times New Roman"
    run.font.size = Pt(11)
    run.font.italic = italic
    run.font.color.rgb = RGBColor(45, 55, 72)
    return p

def add_bullet_item(doc, bold_title, description):
    p = doc.add_paragraph(style='List Bullet')
    p.paragraph_format.space_before = Pt(1)
    p.paragraph_format.space_after = Pt(3)
    p.paragraph_format.line_spacing = 1.15
    p.alignment = WD_ALIGN_PARAGRAPH.JUSTIFY
    r_title = p.add_run(bold_title + ": ")
    r_title.font.name = "Times New Roman"
    r_title.font.size = Pt(11)
    r_title.font.bold = True
    r_title.font.color.rgb = RGBColor(26, 54, 93)
    r_desc = p.add_run(description)
    r_desc.font.name = "Times New Roman"
    r_desc.font.size = Pt(11)
    r_desc.font.color.rgb = RGBColor(45, 55, 72)
    return p

def add_callout(doc, text, title="CATATAN SISTEM & METODOLOGI:"):
    tbl = doc.add_table(rows=1, cols=1)
    tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    c = tbl.rows[0].cells[0]
    set_cell_background(c, "F0F9FF")
    set_cell_margins(c, top=140, bottom=140, left=180, right=180)
    
    tcPr = c._tc.get_or_add_tcPr()
    borders = parse_xml(f'''
        <w:tcBorders {nsdecls("w")}>
            <w:top w:val="none"/>
            <w:left w:val="single" w:sz="24" w:space="0" w:color="0284C7"/>
            <w:bottom w:val="none"/>
            <w:right w:val="none"/>
        </w:tcBorders>
    ''')
    tcPr.append(borders)
    
    p = c.paragraphs[0]
    p.paragraph_format.space_before = Pt(2)
    p.paragraph_format.space_after = Pt(2)
    p.paragraph_format.line_spacing = 1.15
    r_t = p.add_run(title + "\n")
    r_t.font.name = "Times New Roman"
    r_t.font.size = Pt(10.5)
    r_t.font.bold = True
    r_t.font.color.rgb = RGBColor(3, 105, 161)
    
    r_body = p.add_run(text)
    r_body.font.name = "Times New Roman"
    r_body.font.size = Pt(10)
    r_body.font.color.rgb = RGBColor(30, 41, 59)
    doc.add_paragraph().paragraph_format.space_after = Pt(4)

def add_formula_block(doc, formula_text, formula_name=""):
    tbl = doc.add_table(rows=1, cols=1)
    tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    c = tbl.rows[0].cells[0]
    set_cell_background(c, "F8FAFC")
    set_cell_margins(c, top=100, bottom=100, left=160, right=160)
    set_table_borders(tbl, color="E2E8F0", sz="4")
    
    p = c.paragraphs[0]
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p.paragraph_format.space_before = Pt(3)
    p.paragraph_format.space_after = Pt(3)
    
    if formula_name:
        r_name = p.add_run(formula_name + "\n")
        r_name.font.name = "Times New Roman"
        r_name.font.size = Pt(9.5)
        r_name.font.italic = True
        r_name.font.color.rgb = RGBColor(100, 116, 139)
        
    r_f = p.add_run(formula_text)
    r_f.font.name = "Consolas"
    r_f.font.size = Pt(10.5)
    r_f.font.bold = True
    r_f.font.color.rgb = RGBColor(15, 23, 42)
    doc.add_paragraph().paragraph_format.space_after = Pt(2)

def generate_document():
    doc = docx.Document()
    
    # Page Setup: A4, Margins: Top 3cm, Left 4cm, Bottom 3cm, Right 3cm (Standard Skripsi)
    sections = doc.sections
    for section in sections:
        section.page_width = Inches(8.27)   # 21.0 cm
        section.page_height = Inches(11.69) # 29.7 cm
        section.top_margin = Inches(1.18)   # 3.0 cm
        section.bottom_margin = Inches(1.18)# 3.0 cm
        section.left_margin = Inches(1.57)  # 4.0 cm
        section.right_margin = Inches(1.18) # 3.0 cm
        
    print("Writing Document Cover & Header...")
    
    # COVER / HEADER
    p_title = doc.add_paragraph()
    p_title.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_title.paragraph_format.space_before = Pt(24)
    p_title.paragraph_format.space_after = Pt(6)
    r_t1 = p_title.add_run("DOKUMEN PERHITUNGAN LENGKAP METODE DATA MINING\nALGORITMA APRIORI DALAM TAHAPAN KDD\n")
    r_t1.font.name = "Times New Roman"
    r_t1.font.size = Pt(15)
    r_t1.font.bold = True
    r_t1.font.color.rgb = RGBColor(26, 54, 93)
    
    p_sub = doc.add_paragraph()
    p_sub.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_sub.paragraph_format.space_after = Pt(18)
    r_s = p_sub.add_run("PADA SISTEM MONITORING CUSTOMER COMPLAINT & NCR BERBASIS WEB\nSTUDI KASUS: PT WAHANA BERMUDA NUSANTARA\n")
    r_s.font.name = "Times New Roman"
    r_s.font.size = Pt(12)
    r_s.font.bold = True
    r_s.font.color.rgb = RGBColor(43, 108, 176)
    
    # Metadata Box
    meta_tbl = doc.add_table(rows=4, cols=2)
    meta_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    meta_headers = ["Parameter", "Keterangan Dokumen"]
    meta_data = [
        ["Nama Peneliti / Pengembang", "Salsa Shabilla"],
        ["Sistem / Aplikasi", "Salsa-Arpriory (Dashboard Monitoring Customer Complaint & NCR)"],
        ["Objek Penelitian", "Data Keluhan Pelanggan (CCR) & Ketidaksesuaian Produk (PT WBN 2024)"],
        ["Metodologi Utama", "Knowledge Discovery in Databases (KDD) & Algoritma Apriori Native PHP"]
    ]
    format_table(meta_tbl, [2.2, 3.8], meta_headers, meta_data, header_bg="1E293B")
    
    doc.add_paragraph().paragraph_format.space_after = Pt(14)
    
    # =========================================================================
    # BAB I: PENDAHULUAN
    # =========================================================================
    add_heading_1(doc, "BAB I. PENDAHULUAN")
    
    add_heading_2(doc, "1.1 Latar Belakang dan Masalah Mutu di PT Wahana Bermuda Nusantara")
    add_body_p(doc, 
        "PT Wahana Bermuda Nusantara (PT WBN) merupakan perusahaan manufaktur yang bergerak di bidang industri "
        "kemasan dan paper packaging (antara lain produksi paper core, paper tube, dan karton industri). "
        "Sebagai perusahaan yang mengedepankan kepuasan pelanggan dan standar mutu tinggi, Departemen Quality Assurance (QA) "
        "dan Quality Control (QC) rutin mencatat setiap keluhan ketidaksesuaian produk melalui dokumen Customer Complaint Report (CCR) "
        "yang kemudian ditindaklanjuti dengan penerbitan Non-Conformance Report (NCR)."
    )
    add_body_p(doc, 
        "Dalam operasional penanganan keluhan pelanggan sepanjang tahun 2024, ditemukan ratusan laporan komplain terkait "
        "berbagai anomali mutu, seperti penyimpangan dimensi (Length Issue, ID Issue, Thickness Issue), cacat visual (Visual Defect, Printing Defect), "
        "perubahan bentuk fisik (Deformation), hingga kegagalan daya rekat (Adhesion Issue) dan masalah kadar air (Moisture Issue). "
        "Selama ini, proses investigasi akar penyebab keluhan dan penentuan Corrective and Preventive Action (CAPA) "
        "dilakukan secara manual dan parsial. Hal ini menyebabkan bagian QA kesulitan mengenali pola berulang antara jenis cacat "
        "spesifik dengan faktor pemicu utamanya."
    )
    
    add_heading_2(doc, "1.2 Tujuan Penerapan Algoritma Apriori pada Sistem")
    add_body_p(doc, 
        "Untuk menjawab tantangan tersebut, dibangun sistem monitoring berbasis web yang mengintegrasikan alat kendali mutu "
        "(Seven Tools QC) dengan teknik data mining menggunakan Algoritma Apriori. Penerapan algoritma Apriori ini bertujuan untuk:"
    )
    add_bullet_item(doc, "Menemukan Pola Asosiasi Kausalitas", 
                    "Mengekstraksi aturan asosiasi sebab-akibat (association rules) antara Jenis Ketidaksesuaian (Defect) dan Akar Penyebab (Cause) secara objektif berbasis data empiris.")
    add_bullet_item(doc, "Rekomendasi Tindakan Korektif & Preventif Otomatis", 
                    "Menyediakan saran tindakan perbaikan secara real-time pada formulir entri komplain ketika Staff QA memilih jenis kecacatan tertentu.")
    add_bullet_item(doc, "Penetapan Skala Prioritas Penanganan Mutu", 
                    "Mengklasifikasikan aturan temuan ke dalam tingkat prioritas penanganan (Prioritas Utama, Dipertimbangkan, dan Dipantau) berdasarkan frekuensi kemunculan dan kekuatan korelasi (Lift Ratio).")

    add_heading_2(doc, "1.3 Arsitektur Transaksional Data Komplain")
    add_body_p(doc, 
        "Dalam basis data sistem Salsa-Arpriory, relasi data dimodelkan melalui entitas 'Complaints' sebagai header transaksi "
        "dan entitas 'Complaint Items' sebagai rincian ketidaksesuaian. Satu laporan nomor komplain dapat mencakup lebih dari satu "
        "produk atau kombinasi kecacatan dan penyebab. Untuk kepentingan analisis Apriori, setiap pasangan ketidaksesuaian "
        "dan penyebab diperlakukan sebagai item dalam satu keranjang belanja transaksi (market basket transaction)."
    )

    # =========================================================================
    # BAB II: METODOLOGI KNOWLEDGE DISCOVERY IN DATABASES (KDD)
    # =========================================================================
    add_heading_1(doc, "BAB II. METODOLOGI KNOWLEDGE DISCOVERY IN DATABASES (KDD)")
    
    add_body_p(doc, 
        "Knowledge Discovery in Databases (KDD) adalah metodologi terstruktur yang digunakan untuk mengekstrak pengetahuan "
        "dan pola berharga yang sebelumnya tidak diketahui dari sekumpulan data mentah bervolume besar (Fayyad et al., 1996). "
        "Proses KDD dalam penelitian mutu PT Wahana Bermuda Nusantara ini terdiri atas 5 tahapan berkesinambungan:"
    )
    
    add_bullet_item(doc, "1. Data Selection (Seleksi Data)", 
                    "Memilih subset data yang relevan dengan fokus analisis mutu, yaitu catatan Customer Complaint Report tahun 2024.")
    add_bullet_item(doc, "2. Data Pre-processing / Cleaning (Pembersihan Data)", 
                    "Menangani data kosong (missing values), mengeliminasi redundansi, dan menstandarisasi variasi ejaan teknis penulisan cacat dan penyebab.")
    add_bullet_item(doc, "3. Data Transformation (Transformasi Data)", 
                    "Melakukan generalisasi nilai detail ke dalam taksonomi kategori Apriori Ketidaksesuaian dan Apriori Penyebab, serta menyusunnya ke dalam format keranjang transaksi.")
    add_bullet_item(doc, "4. Data Mining (Penambangan Pola dengan Apriori)", 
                    "Mengeksekusi algoritma Apriori secara matematis: menghitung nilai Support itemset tunggal (C1 -> L1), kombinasi pasangan (C2 -> L2), dan mengekstrak aturan asosiasi (Association Rules) berbasis ambang batas Minimum Support, Minimum Confidence, dan Lift Ratio.")
    add_bullet_item(doc, "5. Interpretation / Evaluation (Interpretasi dan Evaluasi)", 
                    "Menganalisis makna teknis dan bisnis dari aturan yang terbentuk, mengkategorikan kekuatan kaitan, serta merumuskan rekomendasi penanganan mutu operasional (CAPA).")

    # =========================================================================
    # BAB III: DETAIL IMPLEMENTASI SETIAP TAHAPAN KDD
    # =========================================================================
    add_heading_1(doc, "BAB III. PERHITUNGAN DAN IMPLEMENTASI KDD LENGKAP")
    
    # -------------------------------------------------------------------------
    # 3.1 Data Selection
    # -------------------------------------------------------------------------
    add_heading_2(doc, "3.1 Tahap 1: Data Selection (Seleksi Data)")
    add_body_p(doc, 
        "Tahap seleksi data dilakukan dengan memilih arsip Customer Complaint Report (CCR) periode 1 Januari 2024 "
        "hingga 31 Desember 2024 dari Departemen QA PT Wahana Bermuda Nusantara. Pada data mentah awal, tercatat sebanyak "
        "162 dokumen kejadian komplain pelanggan. Setelah dilakukan verifikasi bersama supervisor QA, diketahui bahwa "
        "satu nomor keluhan pelanggan dapat mencakup lebih dari satu produk yang dikeluhkan atau memiliki rincian cacat majemuk. "
        "Oleh karena itu, unit analisis yang ditetapkan adalah setiap kejadian produk cacat yang dilaporkan, "
        "sehingga diperoleh total 207 baris record komplain yang siap diproses."
    )
    add_body_p(doc, 
        "Atribut yang diseleksi dari database transaksi meliputi: No Customer, Nama Customer, Tanggal Complain, "
        "Ukuran Produk, Qty Cacat, Jenis Ketidaksesuaian, Detail Ketidaksesuaian, Penyebab, Detail Penyebab, "
        "Corrective Action, Preventive Action, dan Status Penanganan. Tabel 3.1 menyajikan sampel 10 baris representatif "
        "hasil tahap seleksi data."
    )
    
    tbl_sel = doc.add_table(rows=11, cols=6)
    sel_headers = ["No Trx", "Customer", "Ukuran", "Ketidaksesuaian", "Penyebab", "Status"]
    sel_data = [
        ["01-001", "Sapta", "3 x 14 x 924", "Length_Issue", "Masalah Mesin", "Close"],
        ["01-002", "Dharma", "3 x 14 x 924", "Visual_Defect", "Handling", "Close"],
        ["01-003", "Tridharma", "3 x 14 x 924", "Deformation", "Handling", "Close"],
        ["01-004", "Sapta", "4 x 20 x 850", "Length_Issue", "Kondisi Penyimpanan", "Close"],
        ["01-005", "Bintang", "5 x 18 x 1000", "Spec_Issue", "Material Tidak Standar", "Close"],
        ["01-006", "Surya", "3 x 14 x 924", "ID_Issue", "MC Tidak Stabil", "Close"],
        ["01-007", "Dharma", "3 x 14 x 924", "Adhesion_Issue", "Kegagalan Perekatan", "Close"],
        ["01-008", "Tridharma", "4 x 16 x 950", "Deformation", "Handling", "Close"],
        ["01-009", "Sapta", "3 x 14 x 924", "Moisture_Issue", "Proses Drying Tdk Optimal", "Close"],
        ["01-010", "Indopack", "3 x 14 x 924", "Rough_Cut", "Kualitas Cutting", "Close"],
    ]
    format_table(tbl_sel, [0.8, 1.1, 1.2, 1.3, 1.4, 0.7], sel_headers, sel_data)
    add_body_p(doc, "Tabel 3.1: Sampel Data Hasil Seleksi (Data CCR PT Wahana Bermuda Nusantara 2024)", italic=True, align=WD_ALIGN_PARAGRAPH.CENTER)

    # -------------------------------------------------------------------------
    # 3.2 Data Pre-processing / Cleaning
    # -------------------------------------------------------------------------
    add_heading_2(doc, "3.2 Tahap 2: Data Pre-processing / Data Cleaning")
    add_body_p(doc, 
        "Data mentah keluhan pelanggan sering kali mengandung ketidakkonsistenan penulisan akibat dicatat oleh staf "
        "yang berbeda pada shift kerja yang berbeda. Pada tahap pembersihan ini dilakukan:"
    )
    add_bullet_item(doc, "Penanganan Missing Values & Simbol Anomali", 
                    "Pemeriksaan terhadap nilai kosong (NULL), karakter '-' atau 'nan'. Data kosong dikonfirmasikan ulang ke buku log inspeksi QA. Jika tidak dapat dikonfirmasi, baris data tidak disertakan pada analisis asosiasi.")
    add_bullet_item(doc, "Typo Normalization & Case Folding", 
                    "Mengubah semua teks menjadi format standar, membersihkan spasi berlebih (leading/trailing whitespace), dan menyelaraskan istilah bahasa teknis.")
    add_bullet_item(doc, "Penataan Granularitas Data", 
                    "Memastikan setiap baris merepresentasikan pasangan cacat dan penyebab yang logis dan terhubung pada satu nomor laporan komplain.")
    
    add_body_p(doc, "Tabel 3.2 mengilustrasikan contoh standarisasi penulisan sebelum dan sesudah tahap pembersihan data.")
    
    tbl_clean = doc.add_table(rows=6, cols=4)
    clean_headers = ["Field Data", "Nilai Mentah (Sebelum)", "Nilai Bersih (Sesudah)", "Tindakan Koreksi"]
    clean_data = [
        ["Detail Ketidaksesuaian", "L (-) / panjang kurang", "L(-)", "Standarisasi simbol dimensional"],
        ["Detail Ketidaksesuaian", "Pipa Gepeng / penyok", "Gepeng", "Pembersihan sinonim visual"],
        ["Detail Penyebab", "nilon mesin aus / rusak", "Nilon pada mesin aus", "Penyelarasan istilah mekanis"],
        ["Detail Penyebab", "handling tumpukan miring", "Salah penumpukan barang", "Kategorisasi penanganan"],
        ["Keterangan Status", "retur barang / tukar", "Retur", "Standarisasi status NCR"]
    ]
    format_table(tbl_clean, [1.5, 1.6, 1.5, 1.6], clean_headers, clean_data)
    add_body_p(doc, "Tabel 3.2: Contoh Standarisasi dan Pembersihan Data Pre-processing", italic=True, align=WD_ALIGN_PARAGRAPH.CENTER)

    # -------------------------------------------------------------------------
    # 3.3 Data Transformation
    # -------------------------------------------------------------------------
    add_heading_2(doc, "3.3 Tahap 3: Data Transformation (Transformasi Data)")
    add_body_p(doc, 
        "Agar algoritma Apriori dapat menemukan pola yang bermakna dan tidak terpecah menjadi ribuan variasi detail kecil, "
        "tahap transformasi melakukan generalisasi taksonomi (taxonomy generalization) menggunakan kamus pemetaan (lookup dictionary). "
        "Seluruh variasi penulisan detail dikelompokkan ke dalam 11 Kategori Apriori Ketidaksesuaian dan 14 Kategori Apriori Penyebab."
    )
    
    add_heading_3(doc, "A. Kamus Pemetaan Kategori Apriori Ketidaksesuaian (Defect Taxonomy)")
    add_body_p(doc, "Tabel 3.3 menunjukkan pemetaan nilai detail ke dalam 11 kategori Apriori Ketidaksesuaian beserta total kemunculannya pada 207 transaksi.")
    
    tbl_ket = doc.add_table(rows=12, cols=4)
    ket_headers = ["No", "Kategori Apriori Ketidaksesuaian", "Detail Cakupan Masalah", "Frekuensi (Count)"]
    ket_data = [
        ["1", "Length_Issue", "L(+), L(-), L Variasi, Panjang tidak seragam", "58"],
        ["2", "Visual_Defect", "Kotor debu, Bintik lem, Goresan luar, Warna pudar", "29"],
        ["3", "Deformation", "Gepeng, Oval, Bengkok, Melengkung, Bibir mekar", "28"],
        ["4", "ID_Issue", "ID(+), ID(-), Diameter dalam longgar/sesak", "24"],
        ["5", "Adhesion_Issue", "Delaminasi, Lapisan terbuka, Lem kurang rekat", "18"],
        ["6", "Spec_Issue", "Gramatur tidak sesuai, Tebal ply salah, Salah tipe kertas", "15"],
        ["7", "Moisture_Issue", "Kadar air tinggi, Kertas lembab, Berjamur", "11"],
        ["8", "Rough_Cut", "Pemotongan kasar, Serabut ujung pipa, Burry", "8"],
        ["9", "Thickness_Issue", "Ketebalan dinding pipa tidak merata/kurang", "8"],
        ["10", "Printing_Defect", "Cetak logo buram, Posisi printing geser", "5"],
        ["11", "Surface_Damage", "Lecet, Sobek luar, Tergores saat strapping", "3"]
    ]
    format_table(tbl_ket, [0.5, 2.2, 2.8, 1.0], ket_headers, ket_data)
    add_body_p(doc, "Tabel 3.3: Kamus Pemetaan Kategori Apriori Ketidaksesuaian (N = 207)", italic=True, align=WD_ALIGN_PARAGRAPH.CENTER)

    add_heading_3(doc, "B. Kamus Pemetaan Kategori Apriori Penyebab (Cause Taxonomy)")
    add_body_p(doc, "Tabel 3.4 menyajikan 14 kategori Apriori Penyebab hasil generalisasi beserta jumlah kemunculannya.")
    
    tbl_pen = doc.add_table(rows=15, cols=4)
    pen_headers = ["No", "Kategori Apriori Penyebab", "Detail Akar Masalah Lapangan", "Frekuensi (Count)"]
    pen_data = [
        ["1", "Handling", "Penumpukan miring, Terbanting saat muat/bongkar, Strapping terlalu kuat", "53"],
        ["2", "Kondisi Penyimpanan", "Gudang lembab, Sirkulasi udara buruk, Suhu ruang fluktuatif", "29"],
        ["3", "MC Tidak Stabil", "Kecepatan mesin winding naik-turun, Sensor encoder error", "23"],
        ["4", "Material Tidak Standar", "Tension kertas dari supplier variatif, Kertas Kraft basah", "23"],
        ["5", "Proses Drying Tidak Optimal", "Suhu oven kurang panas, Durasi pengeringan kurang lama", "13"],
        ["6", "Material Ex-Stock", "Bahan baku sisa stok lama, Kertas teroksidasi", "11"],
        ["7", "Kegagalan Perekatan", "Viskositas lem encer, Rasio campuran lem tidak homogen", "11"],
        ["8", "Settingan Tidak Tepat", "Jarak pisau potong tidak presisi, Mandrel longgar", "10"],
        ["9", "Human Error", "Operator lalai memeriksa ukuran, Salah membaca lembar kerja SPK", "9"],
        ["10", "Masalah Mesin", "Nilon mesin aus, Belt konveyor selip, Bearing oblak", "8"],
        ["11", "Masalah Pengukuran", "Alat ukur caliper tidak terkalibrasi, Mistar aus", "5"],
        ["12", "Kesalahan Sambungan Core", "Jarak gap sambungan kertas terlalu renggang", "5"],
        ["13", "Dalam monitor", "Penyebab masih dalam investigasi lanjutan tim QC", "4"],
        ["14", "Kualitas Cutting", "Mata pisau potong tumpul, Gerigi pisau rompal", "3"]
    ]
    format_table(tbl_pen, [0.5, 2.2, 2.8, 1.0], pen_headers, pen_data)
    add_body_p(doc, "Tabel 3.4: Kamus Pemetaan Kategori Apriori Penyebab (N = 207)", italic=True, align=WD_ALIGN_PARAGRAPH.CENTER)

    add_heading_3(doc, "C. Pembentukan Keranjang Belanja Transaksi (Market Basket Representation)")
    add_body_p(doc, 
        "Setelah proses generalisasi, setiap transaksi dikonstruksikan menjadi keranjang item unik. "
        "Item diberi penanda prefix 'Ketidaksesuaian=' dan 'Penyebab=' untuk membedakan antara variabel cacat dan akar masalah. "
        "Tabel 3.5 mengilustrasikan representasi biner (One-Hot Encoding) untuk 8 sampel transaksi pertama."
    )
    
    tbl_onehot = doc.add_table(rows=9, cols=7)
    onehot_headers = ["ID Trx", "Length_Issue", "Visual_Defect", "Deformation", "Handling", "Kondisi Simpan", "Mat. Tdk Std"]
    onehot_data = [
        ["T-001", "1", "0", "0", "0", "0", "0"],
        ["T-002", "0", "1", "0", "1", "0", "0"],
        ["T-003", "0", "0", "1", "1", "0", "0"],
        ["T-004", "1", "0", "0", "0", "1", "0"],
        ["T-005", "0", "0", "0", "0", "0", "1"],
        ["T-006", "0", "0", "0", "0", "0", "0"],
        ["T-007", "0", "0", "0", "0", "0", "0"],
        ["T-008", "0", "0", "1", "1", "0", "0"],
    ]
    format_table(tbl_onehot, [0.8, 0.9, 0.9, 0.9, 0.8, 1.0, 1.0], onehot_headers, onehot_data)
    add_body_p(doc, "Tabel 3.5: Representasi Biner (One-Hot Encoding) Sampel Keranjang Transaksi", italic=True, align=WD_ALIGN_PARAGRAPH.CENTER)

    # -------------------------------------------------------------------------
    # 3.4 Data Mining: Algoritma Apriori
    # -------------------------------------------------------------------------
    add_heading_2(doc, "3.4 Tahap 4: Data Mining (Eksekusi Perhitungan Algoritma Apriori)")
    add_body_p(doc, 
        "Tahap Data Mining merupakan inti pemrosesan algoritma Apriori untuk menemukan pola asosiasi frekuensi tinggi "
        "(frequent itemsets) dan aturan asosiasi (association rules). Algoritma Apriori memanfaatkan prinsip dasar Apriori Property: "
        "'Jika suatu itemset bersifat frequent (sering muncul), maka seluruh subset dari itemset tersebut pasti bersifat frequent. "
        "Sebaliknya, jika suatu itemset tidak memenuhi ambang batas minimum support, maka seluruh superset dari itemset tersebut "
        "tidak akan pernah frequent dan dapat dipangkas (pruned).'"
    )
    
    add_heading_3(doc, "A. Landasan Matematis dan Formula Apriori")
    add_body_p(doc, "Perhitungan Apriori dalam sistem ini didasarkan pada empat rumus matematis fundamental:")
    
    add_formula_block(doc, "Support(A) = Count(A) / N", "1. Rumus Support untuk 1-Itemset:")
    add_body_p(doc, "Dimana Count(A) adalah jumlah transaksi yang memuat item A, dan N adalah total seluruh transaksi (N = 207).")
    
    add_formula_block(doc, "Support(A ∪ B) = Count(A ∩ B) / N", "2. Rumus Support untuk Kombinasi 2-Itemset:")
    add_body_p(doc, "Dimana Count(A ∩ B) adalah frekuensi kemunculan item A dan item B secara bersamaan dalam transaksi yang sama.")
    
    add_formula_block(doc, "Confidence(A → B) = Support(A ∪ B) / Support(A) = Count(A ∩ B) / Count(A)", "3. Rumus Tingkat Kepastian (Confidence):")
    add_body_p(doc, "Confidence mengukur seberapa sering item B muncul dalam transaksi yang diketahui memuat item A (probabilitas bersyarat P(B|A)).")
    
    add_formula_block(doc, "Lift(A → B) = Confidence(A → B) / Support(B) = Support(A ∪ B) / [Support(A) × Support(B)]", "4. Rumus Nilai Keterkaitan (Lift Ratio):")
    add_body_p(doc, 
        "Lift Ratio mengukur kekuatan keterkaitan antaritem dibanding probabilitas acak. Jika Lift > 1, hubungan bersifat korelasi "
        "positif yang nyata; jika Lift = 1, kedua item independen (kebetulan); jika Lift < 1, hubungan berkorelasi negatif."
    )

    add_heading_3(doc, "B. Parameter Ambang Batas (Threshold)")
    add_body_p(doc, "Sistem menetapkan parameter ambang batas sebagai berikut:")
    add_bullet_item(doc, "Total Transaksi (N)", "207 transaksi komplain produk.")
    add_bullet_item(doc, "Minimum Support Utama", "5.0% (Ambang batas frekuensi mayor, Count minimal = 0.05 × 207 = 10.35 ≈ 11 transaksi).")
    add_bullet_item(doc, "Minimum Support Granular", "1.5% (Ambang batas deteksi anomali kritis, Count minimal = 0.015 × 207 = 3.105 ≈ 4 transaksi).")
    add_bullet_item(doc, "Minimum Confidence", "50.0% (Tingkat keyakinan bahwa jika terjadi anteseden maka konsekuen ikut terjadi).")

    # Langkah 1: C1
    add_heading_3(doc, "C. Langkah 1: Pembentukan Candidate 1-Itemset (C1)")
    add_body_p(doc, 
        "Candidate 1-itemset (C1) dibentuk dengan menghitung frekuensi kemunculan masing-masing dari 25 item unik "
        "(11 Ketidaksesuaian dan 14 Penyebab) pada seluruh 207 transaksi. Tabel 3.6 menunjukkan rekapitulasi lengkap C1."
    )
    
    tbl_c1 = doc.add_table(rows=26, cols=5)
    c1_headers = ["No", "Nama Item", "Tipe Item", "Frekuensi (Count)", "Nilai Support"]
    c1_data = [
        ["1", "Length_Issue", "Ketidaksesuaian", "58", "28.02%"],
        ["2", "Handling", "Penyebab", "53", "25.60%"],
        ["3", "Visual_Defect", "Ketidaksesuaian", "29", "14.01%"],
        ["4", "Kondisi Penyimpanan", "Penyebab", "29", "14.01%"],
        ["5", "Deformation", "Ketidaksesuaian", "28", "13.53%"],
        ["6", "ID_Issue", "Ketidaksesuaian", "24", "11.59%"],
        ["7", "MC Tidak Stabil", "Penyebab", "23", "11.11%"],
        ["8", "Material Tidak Standar", "Penyebab", "23", "11.11%"],
        ["9", "Adhesion_Issue", "Ketidaksesuaian", "18", "8.70%"],
        ["10", "Spec_Issue", "Ketidaksesuaian", "15", "7.25%"],
        ["11", "Proses Drying Tidak Optimal", "Penyebab", "13", "6.28%"],
        ["12", "Moisture_Issue", "Ketidaksesuaian", "11", "5.31%"],
        ["13", "Material Ex-Stock", "Penyebab", "11", "5.31%"],
        ["14", "Kegagalan Perekatan", "Penyebab", "11", "5.31%"],
        ["15", "Settingan Tidak Tepat", "Penyebab", "10", "4.83%"],
        ["16", "Human Error", "Penyebab", "9", "4.35%"],
        ["17", "Rough_Cut", "Ketidaksesuaian", "8", "3.86%"],
        ["18", "Thickness_Issue", "Ketidaksesuaian", "8", "3.86%"],
        ["19", "Masalah Mesin", "Penyebab", "8", "3.86%"],
        ["20", "Printing_Defect", "Ketidaksesuaian", "5", "2.42%"],
        ["21", "Masalah Pengukuran", "Penyebab", "5", "2.42%"],
        ["22", "Kesalahan Sambungan Core", "Penyebab", "5", "2.42%"],
        ["23", "Dalam monitor", "Penyebab", "4", "1.93%"],
        ["24", "Surface_Damage", "Ketidaksesuaian", "3", "1.45%"],
        ["25", "Kualitas Cutting", "Penyebab", "3", "1.45%"]
    ]
    format_table(tbl_c1, [0.5, 2.4, 1.4, 1.1, 1.0], c1_headers, c1_data)
    add_body_p(doc, "Tabel 3.6: Rekapitulasi Lengkap Candidate 1-Itemset (C1)", italic=True, align=WD_ALIGN_PARAGRAPH.CENTER)

    add_body_p(doc, "Contoh perhitungan manual nilai Support untuk beberapa item pada C1:")
    add_bullet_item(doc, "Support(Length_Issue)", "58 / 207 = 0.280193 = 28.02%")
    add_bullet_item(doc, "Support(Handling)", "53 / 207 = 0.256039 = 25.60%")
    add_bullet_item(doc, "Support(Spec_Issue)", "15 / 207 = 0.072464 = 7.25%")
    add_bullet_item(doc, "Support(Material Tidak Standar)", "23 / 207 = 0.111111 = 11.11%")

    # Langkah 2: L1
    add_heading_3(doc, "D. Langkah 2: Pemangkasan dan Pembentukan Frequent 1-Itemset (L1)")
    add_body_p(doc, 
        "Pada tahap pemangkasan (pruning), item pada C1 yang memiliki Support < 5.0% (Count < 11) dieliminasi. "
        "Dari 25 item pada C1, sebanyak 14 item dinyatakan lolos dan membentuk Frequent 1-Itemset (L1). "
        "Tabel 3.7 merangkum daftar item yang lolos seleksi L1."
    )
    
    tbl_l1 = doc.add_table(rows=15, cols=5)
    l1_headers = ["No", "Nama Frequent Itemset (L1)", "Tipe Variabel", "Count", "Support"]
    l1_data = [
        ["1", "Length_Issue", "Ketidaksesuaian", "58", "28.02%"],
        ["2", "Handling", "Penyebab", "53", "25.60%"],
        ["3", "Visual_Defect", "Ketidaksesuaian", "29", "14.01%"],
        ["4", "Kondisi Penyimpanan", "Penyebab", "29", "14.01%"],
        ["5", "Deformation", "Ketidaksesuaian", "28", "13.53%"],
        ["6", "ID_Issue", "Ketidaksesuaian", "24", "11.59%"],
        ["7", "MC Tidak Stabil", "Penyebab", "23", "11.11%"],
        ["8", "Material Tidak Standar", "Penyebab", "23", "11.11%"],
        ["9", "Adhesion_Issue", "Ketidaksesuaian", "18", "8.70%"],
        ["10", "Spec_Issue", "Ketidaksesuaian", "15", "7.25%"],
        ["11", "Proses Drying Tidak Optimal", "Penyebab", "13", "6.28%"],
        ["12", "Moisture_Issue", "Ketidaksesuaian", "11", "5.31%"],
        ["13", "Material Ex-Stock", "Penyebab", "11", "5.31%"],
        ["14", "Kegagalan Perekatan", "Penyebab", "11", "5.31%"]
    ]
    format_table(tbl_l1, [0.5, 2.4, 1.4, 0.9, 1.0], l1_headers, l1_data)
    add_body_p(doc, "Tabel 3.7: Daftar Anggota Frequent 1-Itemset (L1) (Min Support ≥ 5%)", italic=True, align=WD_ALIGN_PARAGRAPH.CENTER)

    # Langkah 3: C2
    add_heading_3(doc, "E. Langkah 3: Pembentukan Candidate 2-Itemset (C2)")
    add_body_p(doc, 
        "Candidate 2-itemset (C2) dibentuk dengan mengombinasikan pasangan item yang ada pada L1. "
        "Sesuai dengan arsitektur sistem Salsa-Arpriory dan tujuan kausalitas mutu (mencari relasi sebab-akibat), "
        "kombinasi 2 item difokuskan pada pasangan antara 1 Ketidaksesuaian dan 1 Penyebab (bukan sesama ketidaksesuaian). "
        "Tabel 3.8 menyajikan rekapitulasi kombinasi pasangan pada C2."
    )
    
    tbl_c2 = doc.add_table(rows=11, cols=5)
    c2_headers = ["No", "Kombinasi Candidate 2-Itemset", "Count", "Support", "Status (≥ 5%)"]
    c2_data = [
        ["1", "{Length_Issue, Kondisi Penyimpanan}", "26", "12.56%", "Memenuhi"],
        ["2", "{Deformation, Handling}", "25", "12.08%", "Memenuhi"],
        ["3", "{Visual_Defect, Handling}", "22", "10.63%", "Memenuhi"],
        ["4", "{Spec_Issue, Material Tidak Standar}", "13", "6.28%", "Memenuhi"],
        ["5", "{Length_Issue, MC Tidak Stabil}", "13", "6.28%", "Memenuhi"],
        ["6", "{Adhesion_Issue, Kegagalan Perekatan}", "9", "4.35%", "Tidak Memenuhi"],
        ["7", "{ID_Issue, MC Tidak Stabil}", "8", "3.86%", "Tidak Memenuhi"],
        ["8", "{ID_Issue, Material Ex-Stock}", "7", "3.38%", "Tidak Memenuhi"],
        ["9", "{Moisture_Issue, Proses Drying Tdk Opt}", "6", "2.90%", "Tidak Memenuhi"],
        ["10", "{Deformation, Kondisi Penyimpanan}", "2", "0.97%", "Tidak Memenuhi"]
    ]
    format_table(tbl_c2, [0.5, 3.1, 0.8, 0.9, 1.2], c2_headers, c2_data)
    add_body_p(doc, "Tabel 3.8: Sampel Evaluasi Candidate 2-Itemset (C2)", italic=True, align=WD_ALIGN_PARAGRAPH.CENTER)

    # Langkah 4: L2
    add_heading_3(doc, "F. Langkah 4: Pemangkasan dan Pembentukan Frequent 2-Itemset (L2)")
    add_body_p(doc, 
        "Setelah dilakukan penyaringan berdasarkan ambang batas Minimum Support 5.0% (Count ≥ 11), "
        "diperoleh 5 pasangan kombinasi item yang dinyatakan lolos sebagai Frequent 2-Itemset (L2). "
        "Tabel 3.9 menyajikan hasil akhir L2."
    )
    
    tbl_l2 = doc.add_table(rows=6, cols=5)
    l2_headers = ["No", "Frequent 2-Itemset (L2)", "Frekuensi (Count)", "Nilai Support", "Keterangan"]
    l2_data = [
        ["1", "{Length_Issue, Kondisi Penyimpanan}", "26", "12.56%", "Lolos (Count 26 ≥ 11)"],
        ["2", "{Deformation, Handling}", "25", "12.08%", "Lolos (Count 25 ≥ 11)"],
        ["3", "{Visual_Defect, Handling}", "22", "10.63%", "Lolos (Count 22 ≥ 11)"],
        ["4", "{Spec_Issue, Material Tidak Standar}", "13", "6.28%", "Lolos (Count 13 ≥ 11)"],
        ["5", "{Length_Issue, MC Tidak Stabil}", "13", "6.28%", "Lolos (Count 13 ≥ 11)"]
    ]
    format_table(tbl_l2, [0.5, 3.0, 1.0, 0.9, 1.3], l2_headers, l2_data)
    add_body_p(doc, "Tabel 3.9: Daftar Frequent 2-Itemset (L2) yang Memenuhi Min Support 5%", italic=True, align=WD_ALIGN_PARAGRAPH.CENTER)

    # Langkah 5: Analisis K >= 3
    add_heading_3(doc, "G. Langkah 5: Analisis K-Itemset Lebih Tinggi (K ≥ 3) dan Pruning")
    add_body_p(doc, 
        "Berdasarkan kaidah Apriori, untuk membentuk Candidate 3-itemset (C3), diperlukan minimal dua pasangan 2-itemset "
        "pada L2 yang memiliki 1 item sama. Contoh: menggabungkan {Deformation, Handling} dengan {Visual_Defect, Handling} "
        "akan menghasilkan kandidat 3-itemset {Deformation, Visual_Defect, Handling}. "
        "Namun, pada dataset transaksi keluhan pelanggan PT Wahana Bermuda Nusantara, jumlah kemunculan bersamaan ketiga item tersebut "
        "adalah 0 transaksi (karena setiap komplain mengidentifikasi satu kecacatan utama terhadap penanganannya). "
        "Oleh karena itu, frekuensi C3 = 0 < 11 (Support = 0%), sehingga seluruh kandidat 3-itemset langsung tereliminasi "
        "berdasarkan Apriori Pruning Theorem. Proses pembentukan frequent itemset dihentikan pada level k = 2."
    )

    # Langkah 6: Association Rules
    add_heading_3(doc, "H. Langkah 6: Pembentukan Aturan Asosiasi (Association Rules)")
    add_body_p(doc, 
        "Dari setiap anggota L2, dibentuk aturan asosiasi implikasi A → B (anteseden → konsekuen). "
        "Aturan dievaluasi terhadap ambang batas Minimum Confidence ≥ 50% dan dihitung nilai Lift Ratio-nya. "
        "Berikut adalah penjabaran langkah-demi-langkah perhitungan matematis manual untuk setiap aturan yang terbentuk:"
    )

    # Rule 1
    add_body_p(doc, "1. Evaluasi Aturan: Spec_Issue → Material Tidak Standar", bold_prefix="[Aturan 1] ")
    add_bullet_item(doc, "Support Pasangan", "Count(Spec_Issue ∩ Material Tidak Standar) / 207 = 13 / 207 = 0.062802 (6.28%)")
    add_bullet_item(doc, "Confidence", "Count(Spec_Issue ∩ Material Tidak Standar) / Count(Spec_Issue) = 13 / 15 = 0.866667 = 86.67% (Lolos ≥ 50%)")
    add_bullet_item(doc, "Lift Ratio", "Confidence / Support(Material Tidak Standar) = (13/15) / (23/207) = 0.866667 / 0.111111 = 7.800000")
    add_body_p(doc, "Kesimpulan Aturan 1: Nilai Lift 7.80x (> 1) membuktikan bahwa hubungan keterkaitan keduanya 'Sangat Kuat' dan merupakan korelasi nyata.", italic=True)

    # Rule 2
    add_body_p(doc, "2. Evaluasi Aturan: Material Tidak Standar → Spec_Issue", bold_prefix="[Aturan 2] ")
    add_bullet_item(doc, "Support Pasangan", "13 / 207 = 0.062802 (6.28%)")
    add_bullet_item(doc, "Confidence", "Count(Material Tidak Standar ∩ Spec_Issue) / Count(Material Tidak Standar) = 13 / 23 = 0.565217 = 56.52% (Lolos ≥ 50%)")
    add_bullet_item(doc, "Lift Ratio", "Confidence / Support(Spec_Issue) = (13/23) / (15/207) = 0.565217 / 0.072464 = 7.800000")
    add_body_p(doc, "Kesimpulan Aturan 2: Jika ditemukan faktor material yang tidak standar, maka terdapat probabilitas 56.52% akan memicu cacat Spec_Issue (Lift 7.80x, Sangat Kuat).", italic=True)

    # Rule 3
    add_body_p(doc, "3. Evaluasi Aturan: Deformation → Handling", bold_prefix="[Aturan 3] ")
    add_bullet_item(doc, "Support Pasangan", "Count(Deformation ∩ Handling) / 207 = 25 / 207 = 0.120773 (12.08%)")
    add_bullet_item(doc, "Confidence", "Count(Deformation ∩ Handling) / Count(Deformation) = 25 / 28 = 0.892857 = 89.29% (Lolos ≥ 50%)")
    add_bullet_item(doc, "Lift Ratio", "Confidence / Support(Handling) = (25/28) / (53/207) = 0.892857 / 0.256039 = 3.487197 ≈ 3.49")
    add_body_p(doc, "Kesimpulan Aturan 3: Sebanyak 89.29% kasus pipa deformasi (gepeng/oval) bersumber dari faktor penanganan handling operasional (Lift 3.49x, Kuat).", italic=True)

    # Rule 4
    add_body_p(doc, "4. Evaluasi Aturan: Kondisi Penyimpanan → Length_Issue", bold_prefix="[Aturan 4] ")
    add_bullet_item(doc, "Support Pasangan", "Count(Kondisi Penyimpanan ∩ Length_Issue) / 207 = 26 / 207 = 0.125604 (12.56%)")
    add_bullet_item(doc, "Confidence", "Count(Kondisi Penyimpanan ∩ Length_Issue) / Count(Kondisi Penyimpanan) = 26 / 29 = 0.896552 = 89.66% (Lolos ≥ 50%)")
    add_bullet_item(doc, "Lift Ratio", "Confidence / Support(Length_Issue) = (26/29) / (58/207) = 0.896552 / 0.280193 = 3.199762 ≈ 3.20")
    add_body_p(doc, "Kesimpulan Aturan 4: Jika kondisi penyimpanan gudang lembab/panas, sebesar 89.66% memicu penyusutan/pemuaian panjang pipa karton (Lift 3.20x, Kuat).", italic=True)

    # Rule 5
    add_body_p(doc, "5. Evaluasi Aturan: Visual_Defect → Handling", bold_prefix="[Aturan 5] ")
    add_bullet_item(doc, "Support Pasangan", "Count(Visual_Defect ∩ Handling) / 207 = 22 / 207 = 0.106280 (10.63%)")
    add_bullet_item(doc, "Confidence", "Count(Visual_Defect ∩ Handling) / Count(Visual_Defect) = 22 / 29 = 0.758621 = 75.86% (Lolos ≥ 50%)")
    add_bullet_item(doc, "Lift Ratio", "Confidence / Support(Handling) = (22/29) / (53/207) = 0.758621 / 0.256039 = 2.962915 ≈ 2.96")
    add_body_p(doc, "Kesimpulan Aturan 5: Sebanyak 75.86% cacat visual pipa terhubung langsung dengan faktor penanganan handling (Lift 2.96x, Sedang).", italic=True)

    # Rule 6
    add_body_p(doc, "6. Evaluasi Aturan: MC Tidak Stabil → Length_Issue", bold_prefix="[Aturan 6] ")
    add_bullet_item(doc, "Support Pasangan", "Count(MC Tidak Stabil ∩ Length_Issue) / 207 = 13 / 207 = 0.062802 (6.28%)")
    add_bullet_item(doc, "Confidence", "Count(MC Tidak Stabil ∩ Length_Issue) / Count(MC Tidak Stabil) = 13 / 23 = 0.565217 = 56.52% (Lolos ≥ 50%)")
    add_bullet_item(doc, "Lift Ratio", "Confidence / Support(Length_Issue) = (13/23) / (58/207) = 0.565217 / 0.280193 = 2.017241 ≈ 2.02")
    add_body_p(doc, "Kesimpulan Aturan 6: Mesin potong atau winding yang tidak stabil berkontribusi 56.52% terhadap munculnya cacat panjang (Lift 2.02x, Sedang).", italic=True)

    # Rekapitulasi Tabel Rules
    add_body_p(doc, "Tabel 3.10 menyajikan rekapitulasi seluruh association rules utama yang terbentuk beserta metrik evaluasinya.")
    
    tbl_rules = doc.add_table(rows=7, cols=7)
    rule_headers = ["No", "Anteseden (IF)", "Konsekuen (THEN)", "Support", "Confidence", "Lift", "Kekuatan"]
    rule_data = [
        ["1", "Spec_Issue", "Material Tidak Standar", "6.28%", "86.67%", "7.80", "Sangat Kuat"],
        ["2", "Material Tidak Standar", "Spec_Issue", "6.28%", "56.52%", "7.80", "Sangat Kuat"],
        ["3", "Deformation", "Handling", "12.08%", "89.29%", "3.49", "Kuat"],
        ["4", "Kondisi Penyimpanan", "Length_Issue", "12.56%", "89.66%", "3.20", "Kuat"],
        ["5", "Visual_Defect", "Handling", "10.63%", "75.86%", "2.96", "Sedang"],
        ["6", "MC Tidak Stabil", "Length_Issue", "6.28%", "56.52%", "2.02", "Sedang"]
    ]
    format_table(tbl_rules, [0.4, 1.4, 1.6, 0.7, 0.9, 0.6, 0.9], rule_headers, rule_data)
    add_body_p(doc, "Tabel 3.10: Rekapitulasi Association Rules Utama (Min Support 5%, Min Confidence 50%)", italic=True, align=WD_ALIGN_PARAGRAPH.CENTER)

    # Tabel Granular Rules (1.5%)
    add_body_p(doc, 
        "Selain 6 aturan utama di atas, sistem Salsa-Arpriory juga mampu mendeteksi pola kecacatan spesifik "
        "yang frekuensinya lebih rendah namun memiliki dampak kritis terhadap fungsionalitas produk. "
        "Pada batas Minimum Support 1.5% dan Minimum Confidence 50%, sistem menemukan tambahan aturan penting "
        "seperti yang tertera pada Tabel 3.10b."
    )
    
    tbl_subrules = doc.add_table(rows=9, cols=7)
    subrule_headers = ["No", "Anteseden (IF)", "Konsekuen (THEN)", "Support", "Confidence", "Lift", "Kekuatan"]
    subrule_data = [
        ["1", "Kegagalan Perekatan", "Adhesion_Issue", "4.35%", "81.82%", "9.41", "Sangat Kuat"],
        ["2", "Adhesion_Issue", "Kegagalan Perekatan", "4.35%", "50.00%", "9.41", "Sangat Kuat"],
        ["3", "Moisture_Issue", "Proses Drying Tdk Opt", "2.90%", "54.55%", "8.69", "Sangat Kuat"],
        ["4", "Dalam monitor", "Visual_Defect", "1.93%", "100.00%", "7.14", "Sangat Kuat"],
        ["5", "Material Ex-Stock", "ID_Issue", "3.38%", "63.64%", "5.49", "Sangat Kuat"],
        ["6", "Thickness_Issue", "Material Tidak Standar", "1.93%", "50.00%", "4.50", "Kuat"],
        ["7", "Masalah Pengukuran", "Length_Issue", "1.93%", "80.00%", "2.86", "Sedang"],
        ["8", "Settingan Tidak Tepat", "Length_Issue", "2.42%", "50.00%", "1.78", "Sedang"]
    ]
    format_table(tbl_subrules, [0.4, 1.5, 1.5, 0.7, 0.9, 0.6, 0.9], subrule_headers, subrule_data)
    add_body_p(doc, "Tabel 3.10b: Rekapitulasi Aturan Asosiasi Granular (Min Support 1.5%, Min Confidence 50%)", italic=True, align=WD_ALIGN_PARAGRAPH.CENTER)

    # -------------------------------------------------------------------------
    # 3.5 Interpretation & Evaluation
    # -------------------------------------------------------------------------
    add_heading_2(doc, "3.5 Tahap 5: Interpretation & Evaluation (Interpretasi dan Evaluasi)")
    add_body_p(doc, 
        "Tahap akhir dari KDD adalah menafsirkan pola yang ditemukan ke dalam konteks praktis operasional pabrik. "
        "Sistem Salsa-Arpriory tidak hanya menyajikan angka matematis mentah, melainkan mengonversinya menjadi interpretasi "
        "bahasa alami (human-readable interpretation) dan matriks klasifikasi prioritas perbaikan mutu."
    )
    
    add_heading_3(doc, "A. Klasifikasi Kekuatan Kaitan Asosiasi (Lift Metric)")
    add_body_p(doc, "Berdasarkan nilai Lift Ratio, kekuatan korelasi dibagi ke dalam 4 tingkatan standar:")
    add_bullet_item(doc, "Sangat Kuat (Lift ≥ 5.0)", "Korelasi kausalitas sangat tinggi; probabilitas terjadinya konsekuen meningkat lebih dari 5 kali lipat.")
    add_bullet_item(doc, "Kuat (3.0 ≤ Lift < 5.0)", "Korelasi kuat dan konsisten; faktor penyebab merupakan kontributor utama cacat.")
    add_bullet_item(doc, "Sedang (1.5 ≤ Lift < 3.0)", "Korelasi moderat; faktor penyebab turut berpengaruh bersama variabel pendukung lainnya.")
    add_bullet_item(doc, "Lemah (Lift < 1.5)", "Korelasi mendekati acak (independen) dan tidak signifikan untuk dijadikan dasar tindakan pencegahan.")

    add_heading_3(doc, "B. Matriks Klasifikasi Prioritas Penanganan Kualitas")
    add_body_p(doc, 
        "Untuk membantu Supervisor QC dan Manajemen QA mengambil keputusan secara cepat, sistem menetapkan "
        "Matriks Prioritas Mutu berbasis kombinasi Frekuensi Kejadian (Count) dan Kekuatan Kaitan (Lift):"
    )
    add_bullet_item(doc, "Kategori PRIORITAS (Count ≥ 5 dan Lift ≥ 3.0)", 
                    "Aturan yang sangat sering terjadi dan memiliki dampak kausalitas yang kuat. Dijadikan dasar utama tindakan korektif dan pencegahan (CAPA).")
    add_bullet_item(doc, "Kategori DIPERTIMBANGKAN (Count ≥ 5 dan 1.5 ≤ Lift < 3.0)", 
                    "Aturan yang sering terjadi namun keterkaitannya moderat. Dipertimbangkan sebagai faktor pendukung dalam audit operasional berkala.")
    add_bullet_item(doc, "Kategori DIPANTAU (Count 3 - 4 atau Lift < 1.5)", 
                    "Aturan yang terjadi pada data awal atau bersifat sporadis. Dipantau perkembangannya seiring bertambahnya data transaksi baru.")

    add_body_p(doc, "Tabel 3.11 merangkum klasifikasi prioritas dan rekomendasi tindak lanjut sistem Salsa-Arpriory.")
    
    tbl_prio = doc.add_table(rows=8, cols=6)
    prio_headers = ["No", "Aturan Kausalitas Asosiasi", "Count", "Lift", "Kategori Prioritas", "Rekomendasi Tindak Lanjut"]
    prio_data = [
        ["1", "Spec_Issue ➔ Material Tidak Standar", "13", "7.80", "Prioritas", "Dasar utama tindakan koreksi & audit supplier bahan baku"],
        ["2", "Material Tidak Standar ➔ Spec_Issue", "13", "7.80", "Prioritas", "Dasar utama penolakan bahan baku saat Incoming QC"],
        ["3", "Deformation ➔ Handling", "25", "3.49", "Prioritas", "Dasar utama perbaikan SOP handling, packing & gudang"],
        ["4", "Kondisi Simpan ➔ Length_Issue", "26", "3.20", "Prioritas", "Dasar utama pengendalian kelembaban gudang pipa core"],
        ["5", "Visual_Defect ➔ Handling", "22", "2.96", "Dipertimbangkan", "Faktor pendukung evaluasi cara penanganan operator"],
        ["6", "MC Tdk Stabil ➔ Length_Issue", "13", "2.02", "Dipertimbangkan", "Faktor pendukung kalibrasi kecepatan mesin winding"],
        ["7", "Kegagalan Lem ➔ Adhesion_Issue", "9", "9.41", "Prioritas", "Dasar utama kontrol viskositas lem perekat paper tube"]
    ]
    format_table(tbl_prio, [0.4, 2.0, 0.6, 0.6, 1.2, 1.7], prio_headers, prio_data)
    add_body_p(doc, "Tabel 3.11: Matriks Klasifikasi Prioritas Penanganan Mutu dan Rekomendasi", italic=True, align=WD_ALIGN_PARAGRAPH.CENTER)

    add_heading_3(doc, "C. Rencana Tindakan Korektif & Preventif (CAPA) Berbasis Temuan Apriori")
    add_body_p(doc, 
        "Berdasarkan pola asosiasi yang telah divalidasi, berikut adalah rekomendasi tindakan nyata yang dapat "
        "diterapkan oleh Departemen Quality Control di PT Wahana Bermuda Nusantara:"
    )
    
    add_bullet_item(doc, "1. Penanganan Deformasi (Deformation) dan Cacat Visual akibat Handling", 
                    "Pemberian bantalan busa (cushion foam) pada palet kayu, pembatasan tinggi maksimal tumpukan pipa karton (maksimal 5 layer), serta penggunaan pelindung sudut (corner protector) saat proses strapping kemasan untuk mencegah pipa menjadi gepeng atau tergores.")
    
    add_bullet_item(doc, "2. Pengendalian Masalah Panjang (Length Issue) akibat Kondisi Penyimpanan & Mesin", 
                    "Pemasangan alat pengatur kelembaban udara (dehumidifier) dan hygrometer digital di area gudang finished goods. Kertas pipa bersifat higroskopis sehingga rentan memuai/menyusut jika terpapar kelembaban ekstrem. Selain itu, sensor encoder pengukur panjang pada mesin potong wajib dikalibrasi mingguan.")
    
    add_bullet_item(doc, "3. Penanganan Ketidaksesuaian Spesifikasi (Spec Issue) akibat Material Tidak Standar", 
                    "Pengetatan inspeksi bahan baku masuk (Incoming Raw Material Inspection). Pengujian gramatur kertas (GSM) dan tes Cobb (daya serap air) wajib dilakukan sebelum gulungan kertas diizinkan masuk ke lini produksi.")
    
    add_bullet_item(doc, "4. Penanganan Masalah Perekatan (Adhesion Issue) akibat Kegagalan Perekatan", 
                    "Pemeriksaan berkala terhadap rasio pencampuran bahan perekat (starch/dextrin adhesive) dan viskositas lem menggunakan viscometer sebelum mesin beroperasi, guna mencegah pipa karton terkelupas (delaminasi).")

    add_heading_3(doc, "D. Integrasi Hasil Apriori dengan Seven Tools QC dan Sistem NCR")
    add_body_p(doc, 
        "Dalam sistem Salsa-Arpriory, hasil analisis Apriori dihubungkan secara sinergis dengan alat kendali mutu Seven Tools:"
    )
    add_bullet_item(doc, "Diagram Pareto", "Mengidentifikasi 20% cacat terbesar yang menyumbang 80% masalah komplain (misal Length_Issue dan Deformation).")
    add_bullet_item(doc, "Diagram Fishbone (6M)", "Aturan Apriori memetakan akar penyebab ke dalam kategori Fishbone 6M (Man, Machine, Material, Method, Environment, Measurement) secara otomatis.")
    add_bullet_item(doc, "Form Rekomendasi CAPA Pintar", "Saat Staff QA menginput komplain baru dan memilih jenis cacat 'Deformation', sistem secara otomatis menampilkan jendela rekomendasi bahwa akar penyebabnya kemungkinan besar adalah 'Handling' dengan confidence 89% dan menyarankan tindakan korektif tukar guling serta perbaikan tumpukan barang.")

    # =========================================================================
    # BAB IV: KESIMPULAN & PENUTUP
    # =========================================================================
    add_heading_1(doc, "BAB IV. KESIMPULAN")
    add_body_p(doc, 
        "Berdasarkan keseluruhan perhitungan dan pengujian data mining menggunakan algoritma Apriori dalam kerangka "
        "Knowledge Discovery in Databases (KDD) pada data keluhan pelanggan PT Wahana Bermuda Nusantara, diperoleh kesimpulan sebagai berikut:"
    )
    add_bullet_item(doc, "1. Efektivitas Metodologi KDD", 
                    "Penerapan 5 tahapan KDD (Data Selection, Pre-processing, Transformation, Data Mining, dan Interpretation) berhasil mengolah 162 laporan keluhan awal menjadi 207 transaksi produk komplain yang bersih, terstandarisasi, dan siap ditambang tanpa noise.")
    add_bullet_item(doc, "2. Pola Asosiasi Dominan", 
                    "Algoritma Apriori berhasil menemukan 6 aturan asosiasi utama pada batas Minimum Support 5% dan Minimum Confidence 50%. Aturan dengan korelasi terkuat adalah Spec_Issue ➔ Material Tidak Standar (Confidence 86.67%, Lift 7.80x), Deformation ➔ Handling (Confidence 89.29%, Lift 3.49x), dan Kondisi Penyimpanan ➔ Length_Issue (Confidence 89.66%, Lift 3.20x).")
    add_bullet_item(doc, "3. Nilai Lift Ratio Positif", 
                    "Seluruh aturan yang terbentuk memiliki nilai Lift Ratio jauh di atas 1 (berkisar antara 2.02 hingga 7.80x, bahkan mencapai 9.41x pada aturan granular), membuktikan bahwa pola yang ditemukan bukan merupakan kebetulan melainkan hubungan kausalitas yang sangat valid secara statistik.")
    add_bullet_item(doc, "4. Keunggulan Arsitektur Sistem Salsa-Arpriory", 
                    "Algoritma Apriori yang dibangun secara murni (native PHP) pada sistem ini mampu beroperasi secara independen tanpa bergantung pada runtime eksternal (Python), menghasilkan komputasi real-time yang langsung menyinkronkan metrik setiap kali data komplain bertambah, serta terintegrasi langsung dengan modul penerbitan dokumen resmi Non-Conformance Report (NCR) dan Seven Tools QC.")

    add_callout(doc, 
        "Seluruh hasil perhitungan matematis yang terdokumentasi dalam berkas ini telah tervalidasi dan identik "
        "antara implementasi sistem web Salsa-Arpriory (AprioriService.php) dan skrip analisis python (apriori_complain.py) "
        "dengan dataset resmi PT Wahana Bermuda Nusantara tahun 2024.",
        "VALIDASI HASIL PERHITUNGAN SISTEM:"
    )

    # Save to _bahan-skripsi and artifact dir
    out_path = r"c:\laragon\www\Apriori\salsa-arpriory\_bahan-skripsi\PERHITUNGAN_LENGKAP_APRIORI_KDD_SALSA.docx"
    doc.save(out_path)
    print(f"File successfully created: {out_path}")
    
    # Also save to current directory for easy access
    local_path = "PERHITUNGAN_LENGKAP_APRIORI_KDD_SALSA.docx"
    doc.save(local_path)
    print(f"File also copied to: {local_path}")

if __name__ == "__main__":
    generate_document()
