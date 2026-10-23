import xml.etree.ElementTree as ET

xml_content = '''<mxfile host="app.diagrams.net" modified="2026-10-06T15:15:00.000Z" agent="Mozilla/5.0" version="21.6.8" type="device">
  <diagram id="wireframe-dashboard-supervisor-bw" name="Wireframe - Dashboard Supervisor QC (Hitam Putih)">
    <mxGraphModel dx="1400" dy="1800" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1320" pageHeight="1880" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />

        <!-- ========================================== -->
        <!-- BROWSER / APP WINDOW FRAME CONTAINER       -->
        <!-- ========================================== -->
        <mxCell id="win_bg" value="" style="rounded=0;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;strokeWidth=1.5;" vertex="1" parent="1">
          <mxGeometry x="20" y="20" width="1280" height="1820" as="geometry" />
        </mxCell>

        <!-- Browser Header Bar -->
        <mxCell id="win_header" value="" style="rounded=0;whiteSpace=wrap;html=1;fillColor=#f5f5f5;strokeColor=#000000;strokeWidth=1;" vertex="1" parent="1">
          <mxGeometry x="20" y="20" width="1280" height="36" as="geometry" />
        </mxCell>
        <!-- Browser Dots -->
        <mxCell id="dot_1" value="" style="ellipse;fillColor=#ffffff;strokeColor=#000000;strokeWidth=1;" vertex="1" parent="1">
          <mxGeometry x="36" y="32" width="12" height="12" as="geometry" />
        </mxCell>
        <mxCell id="dot_2" value="" style="ellipse;fillColor=#cccccc;strokeColor=#000000;strokeWidth=1;" vertex="1" parent="1">
          <mxGeometry x="54" y="32" width="12" height="12" as="geometry" />
        </mxCell>
        <mxCell id="dot_3" value="" style="ellipse;fillColor=#666666;strokeColor=#000000;strokeWidth=1;" vertex="1" parent="1">
          <mxGeometry x="72" y="32" width="12" height="12" as="geometry" />
        </mxCell>
        <!-- URL Bar -->
        <mxCell id="win_url" value="http://localhost:8000/dashboard (Wireframe Dashboard Supervisor QC - Hitam Putih)" style="rounded=4;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#cccccc;strokeWidth=1;fontSize=11;fontFamily=Helvetica;fontColor=#333333;align=left;spacingLeft=10;" vertex="1" parent="1">
          <mxGeometry x="110" y="26" width="1170" height="24" as="geometry" />
        </mxCell>

        <!-- ========================================== -->
        <!-- TOPBAR NAVIGATION (HITAM PUTIH)           -->
        <!-- ========================================== -->
        <mxCell id="topbar" value="" style="rounded=0;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;strokeWidth=1;" vertex="1" parent="1">
          <mxGeometry x="240" y="56" width="1060" height="54" as="geometry" />
        </mxCell>
        <mxCell id="topbar_title" value="Dashboard Supervisor QC" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;whiteSpace=wrap;rounded=0;fontSize=15;fontStyle=1;fontFamily=Helvetica;fontColor=#000000;" vertex="1" parent="1">
          <mxGeometry x="260" y="62" width="300" height="24" as="geometry" />
        </mxCell>
        <mxCell id="topbar_sub" value="Monitoring Manajerial · NCR Approval · Kunjungan · Analisis Kualitas" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontFamily=Helvetica;fontColor=#666666;" vertex="1" parent="1">
          <mxGeometry x="260" y="84" width="450" height="18" as="geometry" />
        </mxCell>
        <!-- Topbar Button: Laporan PDF -->
        <mxCell id="topbar_btn_pdf" value="📄 Laporan PDF" style="rounded=6;whiteSpace=wrap;html=1;fillColor=#000000;strokeColor=#000000;strokeWidth=1;fontSize=11;fontFamily=Helvetica;fontStyle=1;fontColor=#ffffff;align=center;" vertex="1" parent="1">
          <mxGeometry x="1115" y="66" width="130" height="34" as="geometry" />
        </mxCell>
        <mxCell id="topbar_bell" value="🔔" style="text;html=1;strokeColor=none;fillColor=none;align=center;verticalAlign=middle;fontSize=15;fontColor=#000000;" vertex="1" parent="1">
          <mxGeometry x="1255" y="68" width="30" height="30" as="geometry" />
        </mxCell>

        <!-- ========================================== -->
        <!-- SIDEBAR (LEFT NAVIGATION - SOLID BLACK)    -->
        <!-- ========================================== -->
        <mxCell id="sidebar" value="" style="rounded=0;whiteSpace=wrap;html=1;fillColor=#000000;strokeColor=#000000;strokeWidth=1;" vertex="1" parent="1">
          <mxGeometry x="20" y="56" width="220" height="1784" as="geometry" />
        </mxCell>

        <!-- Sidebar Brand / Logo Area -->
        <mxCell id="sb_brand_logo" value="W" style="ellipse;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;strokeWidth=1.5;fontSize=16;fontStyle=1;fontFamily=Helvetica;fontColor=#000000;align=center;" vertex="1" parent="1">
          <mxGeometry x="36" y="74" width="34" height="34" as="geometry" />
        </mxCell>
        <mxCell id="sb_brand_title" value="WberPack" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=16;fontStyle=1;fontFamily=Helvetica;fontColor=#ffffff;" vertex="1" parent="1">
          <mxGeometry x="78" y="72" width="150" height="24" as="geometry" />
        </mxCell>
        <mxCell id="sb_brand_sub" value="Monitoring Complaint &amp; NCR" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontStyle=1;fontFamily=Helvetica;fontColor=#cccccc;" vertex="1" parent="1">
          <mxGeometry x="36" y="112" width="190" height="20" as="geometry" />
        </mxCell>

        <!-- Sidebar Menu Items (Supervisor Role) -->
        <mxCell id="sb_menu_spv_active" value="🏠  Dashboard SPV" style="rounded=8;whiteSpace=wrap;html=1;fillColor=#333333;strokeColor=#ffffff;strokeWidth=1;align=left;spacingLeft=15;fontSize=11;fontFamily=Helvetica;fontStyle=1;fontColor=#ffffff;" vertex="1" parent="1">
          <mxGeometry x="30" y="155" width="200" height="38" as="geometry" />
        </mxCell>
        <mxCell id="sb_menu_val" value="📁  Validasi &amp; Approval NCR" style="rounded=4;whiteSpace=wrap;html=1;fillColor=none;strokeColor=none;align=left;spacingLeft=15;fontSize=11;fontFamily=Helvetica;fontColor=#cccccc;" vertex="1" parent="1">
          <mxGeometry x="30" y="200" width="200" height="36" as="geometry" />
        </mxCell>
        <mxCell id="sb_menu_7tools" value="≡   Seven Tools QC" style="rounded=4;whiteSpace=wrap;html=1;fillColor=none;strokeColor=none;align=left;spacingLeft=15;fontSize=11;fontFamily=Helvetica;fontColor=#cccccc;" vertex="1" parent="1">
          <mxGeometry x="30" y="244" width="200" height="36" as="geometry" />
        </mxCell>
        <mxCell id="sb_menu_apriori" value="📈  Analisis Apriori" style="rounded=4;whiteSpace=wrap;html=1;fillColor=none;strokeColor=none;align=left;spacingLeft=15;fontSize=11;fontFamily=Helvetica;fontColor=#cccccc;" vertex="1" parent="1">
          <mxGeometry x="30" y="288" width="200" height="36" as="geometry" />
        </mxCell>
        <mxCell id="sb_menu_laporan" value="📊  Laporan Visual Eksekutif" style="rounded=4;whiteSpace=wrap;html=1;fillColor=none;strokeColor=none;align=left;spacingLeft=15;fontSize=11;fontFamily=Helvetica;fontColor=#cccccc;" vertex="1" parent="1">
          <mxGeometry x="30" y="332" width="200" height="36" as="geometry" />
        </mxCell>
        <mxCell id="sb_menu_visit" value="🚐  Kelola Visit Customer" style="rounded=4;whiteSpace=wrap;html=1;fillColor=none;strokeColor=none;align=left;spacingLeft=15;fontSize=11;fontFamily=Helvetica;fontColor=#cccccc;" vertex="1" parent="1">
          <mxGeometry x="30" y="376" width="200" height="36" as="geometry" />
        </mxCell>

        <!-- Sidebar User Profile Footer (Supervisor QC) -->
        <mxCell id="sb_user_box" value="" style="rounded=8;whiteSpace=wrap;html=1;fillColor=#1a1a1a;strokeColor=#333333;strokeWidth=1;" vertex="1" parent="1">
          <mxGeometry x="30" y="1640" width="200" height="180" as="geometry" />
        </mxCell>
        <mxCell id="sb_user_avatar" value="S" style="ellipse;whiteSpace=wrap;html=1;fillColor=#333333;strokeColor=#ffffff;strokeWidth=1;fontSize=14;fontStyle=1;fontColor=#ffffff;align=center;" vertex="1" parent="1">
          <mxGeometry x="42" y="1652" width="34" height="34" as="geometry" />
        </mxCell>
        <mxCell id="sb_user_name" value="Supervisor QC" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=12;fontStyle=1;fontFamily=Helvetica;fontColor=#ffffff;" vertex="1" parent="1">
          <mxGeometry x="84" y="1652" width="130" height="18" as="geometry" />
        </mxCell>
        <mxCell id="sb_user_role" value="SUPERVISOR QC" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=9;fontStyle=1;fontFamily=Helvetica;fontColor=#aaaaaa;" vertex="1" parent="1">
          <mxGeometry x="84" y="1670" width="130" height="14" as="geometry" />
        </mxCell>
        <mxCell id="sb_btn_pwd" value="🔑 Ganti Password" style="rounded=6;whiteSpace=wrap;html=1;fillColor=#333333;strokeColor=#666666;strokeWidth=1;fontSize=10;fontFamily=Helvetica;fontColor=#ffffff;align=center;" vertex="1" parent="1">
          <mxGeometry x="42" y="1710" width="176" height="34" as="geometry" />
        </mxCell>
        <mxCell id="sb_btn_logout" value="➔ Keluar" style="rounded=6;whiteSpace=wrap;html=1;fillColor=#333333;strokeColor=#666666;strokeWidth=1;fontSize=10;fontFamily=Helvetica;fontColor=#ffffff;align=center;" vertex="1" parent="1">
          <mxGeometry x="42" y="1756" width="176" height="34" as="geometry" />
        </mxCell>


        <!-- ==============================================================
             MAIN CONTENT AREA (x=260, width=1020)
        ============================================================== -->

        <!-- ==================== 1. ACTION BANNERS ==================== -->
        <!-- BANNER 1: APPROVAL PENDING -->
        <mxCell id="banner_1" value="" style="rounded=8;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;strokeWidth=1.5;dashed=1;" vertex="1" parent="1">
          <mxGeometry x="260" y="125" width="1020" height="52" as="geometry" />
        </mxCell>
        <mxCell id="b1_icon_circle" value="" style="ellipse;whiteSpace=wrap;html=1;aspect=fixed;fillColor=#f4f4f4;strokeColor=#000000;strokeWidth=1;" vertex="1" parent="1">
          <mxGeometry x="274" y="134" width="34" height="34" as="geometry" />
        </mxCell>
        <mxCell id="b1_icon_hg" value="◷" style="text;html=1;strokeColor=none;fillColor=none;align=center;verticalAlign=middle;fontSize=18;fontStyle=1;fontColor=#000000;" vertex="1" parent="1">
          <mxGeometry x="274" y="134" width="34" height="34" as="geometry" />
        </mxCell>
        <mxCell id="b1_title" value="APPROVAL PENDING: 1 Dokumen NCR Menunggu Persetujuan Anda!" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=11;fontStyle=1;fontColor=#000000;fontFamily=Helvetica;" vertex="1" parent="1">
          <mxGeometry x="320" y="131" width="580" height="18" as="geometry" />
        </mxCell>
        <mxCell id="b1_sub" value="Tinjau kelayakan data investigasi 6M &amp; CAPA sebelum memberikan approval." style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontColor=#555555;fontFamily=Helvetica;" vertex="1" parent="1">
          <mxGeometry x="320" y="149" width="580" height="16" as="geometry" />
        </mxCell>
        <mxCell id="btn_b1" value="Tinjau Kasus Pending →" style="rounded=6;whiteSpace=wrap;html=1;fillColor=#000000;strokeColor=#000000;fontColor=#ffffff;fontSize=10;fontStyle=1;align=center;" vertex="1" parent="1">
          <mxGeometry x="1115" y="136" width="150" height="30" as="geometry" />
        </mxCell>

        <!-- BANNER 2: JADWAL VISIT TERDEKAT -->
        <mxCell id="banner_2" value="" style="rounded=8;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;strokeWidth=1.2;" vertex="1" parent="1">
          <mxGeometry x="260" y="184" width="1020" height="46" as="geometry" />
        </mxCell>
        <mxCell id="b2_icon_circle" value="" style="ellipse;whiteSpace=wrap;html=1;aspect=fixed;fillColor=#f4f4f4;strokeColor=#000000;strokeWidth=1;" vertex="1" parent="1">
          <mxGeometry x="274" y="191" width="32" height="32" as="geometry" />
        </mxCell>
        <mxCell id="b2_icon_van" value="🚐" style="text;html=1;strokeColor=none;fillColor=none;align=center;verticalAlign=middle;fontSize=15;fontStyle=1;fontColor=#000000;" vertex="1" parent="1">
          <mxGeometry x="274" y="191" width="32" height="32" as="geometry" />
        </mxCell>
        <mxCell id="b2_title" value="JADWAL VISIT: 4 Kunjungan Lapangan Aktif — Terdekat: PT Astra Honda Motor (12 Okt 2026 · HARI INI)" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=11;fontStyle=1;fontColor=#000000;fontFamily=Helvetica;" vertex="1" parent="1">
          <mxGeometry x="320" y="198" width="670" height="18" as="geometry" />
        </mxCell>
        <mxCell id="btn_b2" value="Buka Kalender Visit →" style="text;html=1;strokeColor=none;fillColor=none;align=right;verticalAlign=middle;fontSize=10;fontStyle=1;fontColor=#000000;fontFamily=Helvetica;" vertex="1" parent="1">
          <mxGeometry x="1125" y="197" width="140" height="20" as="geometry" />
        </mxCell>


        <!-- ==================== 2. EXECUTIVE KPI CARDS (4x2 GRID) ==================== -->
        <!-- ROW 1 CARDS (y=238, h=72, w=243, gap=16) -->
        <mxCell id="card_1" value="" style="rounded=8;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;strokeWidth=1.2;" vertex="1" parent="1">
          <mxGeometry x="260" y="238" width="243" height="72" as="geometry" />
        </mxCell>
        <mxCell id="c1_dot" value="" style="ellipse;whiteSpace=wrap;html=1;aspect=fixed;fillColor=#000000;strokeColor=none;" vertex="1" parent="1"><mxGeometry x="273" y="248" width="7" height="7" as="geometry" /></mxCell>
        <mxCell id="c1_lbl" value="Total Complaint" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontStyle=1;fontColor=#000000;fontFamily=Helvetica;" vertex="1" parent="1"><mxGeometry x="285" y="244" width="140" height="16" as="geometry" /></mxCell>
        <mxCell id="c1_arrow" value="→" style="text;html=1;strokeColor=none;fillColor=none;align=right;verticalAlign=middle;fontSize=11;fontColor=#888888;" vertex="1" parent="1"><mxGeometry x="475" y="244" width="18" height="16" as="geometry" /></mxCell>
        <mxCell id="c1_val" value="182" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=18;fontStyle=1;fontColor=#000000;fontFamily=Helvetica;" vertex="1" parent="1"><mxGeometry x="273" y="261" width="100" height="24" as="geometry" /></mxCell>
        <mxCell id="c1_sub" value="kasus (all time)" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=9;fontColor=#666666;fontFamily=Helvetica;" vertex="1" parent="1"><mxGeometry x="273" y="287" width="140" height="14" as="geometry" /></mxCell>

        <!-- Card 2: NCR Divalidasi -->
        <mxCell id="card_2" value="" style="rounded=8;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;strokeWidth=1.2;" vertex="1" parent="1">
          <mxGeometry x="519" y="238" width="243" height="72" as="geometry" />
        </mxCell>
        <mxCell id="c2_dot" value="" style="ellipse;whiteSpace=wrap;html=1;aspect=fixed;fillColor=#000000;strokeColor=none;" vertex="1" parent="1"><mxGeometry x="532" y="248" width="7" height="7" as="geometry" /></mxCell>
        <mxCell id="c2_lbl" value="NCR Divalidasi [✓]" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontStyle=1;fontColor=#000000;fontFamily=Helvetica;" vertex="1" parent="1"><mxGeometry x="544" y="244" width="140" height="16" as="geometry" /></mxCell>
        <mxCell id="c2_arrow" value="→" style="text;html=1;strokeColor=none;fillColor=none;align=right;verticalAlign=middle;fontSize=11;fontColor=#888888;" vertex="1" parent="1"><mxGeometry x="734" y="244" width="18" height="16" as="geometry" /></mxCell>
        <mxCell id="c2_val" value="181" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=18;fontStyle=1;fontColor=#000000;fontFamily=Helvetica;" vertex="1" parent="1"><mxGeometry x="532" y="261" width="100" height="24" as="geometry" /></mxCell>
        <mxCell id="c2_sub" value="dokumen approved" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=9;fontColor=#666666;fontFamily=Helvetica;" vertex="1" parent="1"><mxGeometry x="532" y="287" width="140" height="14" as="geometry" /></mxCell>

        <!-- Card 3: NCR Pending -->
        <mxCell id="card_3" value="" style="rounded=8;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;strokeWidth=1.2;" vertex="1" parent="1">
          <mxGeometry x="778" y="238" width="243" height="72" as="geometry" />
        </mxCell>
        <mxCell id="c3_dot" value="" style="ellipse;whiteSpace=wrap;html=1;aspect=fixed;fillColor=#000000;strokeColor=none;" vertex="1" parent="1"><mxGeometry x="791" y="248" width="7" height="7" as="geometry" /></mxCell>
        <mxCell id="c3_lbl" value="NCR Pending [◷]" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontStyle=1;fontColor=#000000;fontFamily=Helvetica;" vertex="1" parent="1"><mxGeometry x="803" y="244" width="150" height="16" as="geometry" /></mxCell>
        <mxCell id="c3_arrow" value="→" style="text;html=1;strokeColor=none;fillColor=none;align=right;verticalAlign=middle;fontSize=11;fontColor=#888888;" vertex="1" parent="1"><mxGeometry x="993" y="244" width="18" height="16" as="geometry" /></mxCell>
        <mxCell id="c3_val" value="1" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=18;fontStyle=1;fontColor=#000000;fontFamily=Helvetica;" vertex="1" parent="1"><mxGeometry x="791" y="261" width="100" height="24" as="geometry" /></mxCell>
        <mxCell id="c3_sub" value="menunggu review SPV" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=9;fontColor=#666666;fontFamily=Helvetica;" vertex="1" parent="1"><mxGeometry x="791" y="287" width="140" height="14" as="geometry" /></mxCell>

        <!-- Card 4: NCR Revisi -->
        <mxCell id="card_4" value="" style="rounded=8;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;strokeWidth=1.2;" vertex="1" parent="1">
          <mxGeometry x="1037" y="238" width="243" height="72" as="geometry" />
        </mxCell>
        <mxCell id="c4_dot" value="" style="ellipse;whiteSpace=wrap;html=1;aspect=fixed;fillColor=#000000;strokeColor=none;" vertex="1" parent="1"><mxGeometry x="1050" y="248" width="7" height="7" as="geometry" /></mxCell>
        <mxCell id="c4_lbl" value="NCR Revisi [↺]" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontStyle=1;fontColor=#000000;fontFamily=Helvetica;" vertex="1" parent="1"><mxGeometry x="1062" y="244" width="150" height="16" as="geometry" /></mxCell>
        <mxCell id="c4_arrow" value="→" style="text;html=1;strokeColor=none;fillColor=none;align=right;verticalAlign=middle;fontSize=11;fontColor=#888888;" vertex="1" parent="1"><mxGeometry x="1252" y="244" width="18" height="16" as="geometry" /></mxCell>
        <mxCell id="c4_val" value="0" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=18;fontStyle=1;fontColor=#000000;fontFamily=Helvetica;" vertex="1" parent="1"><mxGeometry x="1050" y="261" width="100" height="24" as="geometry" /></mxCell>
        <mxCell id="c4_sub" value="dikembalikan ke QA" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=9;fontColor=#666666;fontFamily=Helvetica;" vertex="1" parent="1"><mxGeometry x="1050" y="287" width="140" height="14" as="geometry" /></mxCell>

        <!-- ROW 2 CARDS (y=318, h=72, w=243, gap=16) -->
        <mxCell id="card_5" value="" style="rounded=8;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;strokeWidth=1.2;" vertex="1" parent="1">
          <mxGeometry x="260" y="318" width="243" height="72" as="geometry" />
        </mxCell>
        <mxCell id="c5_dot" value="" style="ellipse;whiteSpace=wrap;html=1;aspect=fixed;fillColor=#000000;strokeColor=none;" vertex="1" parent="1"><mxGeometry x="273" y="328" width="7" height="7" as="geometry" /></mxCell>
        <mxCell id="c5_lbl" value="Status Close" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontStyle=1;fontColor=#000000;fontFamily=Helvetica;" vertex="1" parent="1"><mxGeometry x="285" y="324" width="140" height="16" as="geometry" /></mxCell>
        <mxCell id="c5_arrow" value="→" style="text;html=1;strokeColor=none;fillColor=none;align=right;verticalAlign=middle;fontSize=11;fontColor=#888888;" vertex="1" parent="1"><mxGeometry x="475" y="324" width="18" height="16" as="geometry" /></mxCell>
        <mxCell id="c5_val" value="180 / 182" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=18;fontStyle=1;fontColor=#000000;fontFamily=Helvetica;" vertex="1" parent="1"><mxGeometry x="273" y="341" width="130" height="24" as="geometry" /></mxCell>
        <mxCell id="c5_sub" value="98.9% selesai" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=9;fontColor=#666666;fontFamily=Helvetica;" vertex="1" parent="1"><mxGeometry x="273" y="367" width="140" height="14" as="geometry" /></mxCell>

        <!-- Card 6: Jumlah Customer -->
        <mxCell id="card_6" value="" style="rounded=8;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;strokeWidth=1.2;" vertex="1" parent="1">
          <mxGeometry x="519" y="318" width="243" height="72" as="geometry" />
        </mxCell>
        <mxCell id="c6_dot" value="" style="ellipse;whiteSpace=wrap;html=1;aspect=fixed;fillColor=#000000;strokeColor=none;" vertex="1" parent="1"><mxGeometry x="532" y="328" width="7" height="7" as="geometry" /></mxCell>
        <mxCell id="c6_lbl" value="Jumlah Customer" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontStyle=1;fontColor=#000000;fontFamily=Helvetica;" vertex="1" parent="1"><mxGeometry x="544" y="324" width="140" height="16" as="geometry" /></mxCell>
        <mxCell id="c6_arrow" value="→" style="text;html=1;strokeColor=none;fillColor=none;align=right;verticalAlign=middle;fontSize=11;fontColor=#888888;" vertex="1" parent="1"><mxGeometry x="734" y="324" width="18" height="16" as="geometry" /></mxCell>
        <mxCell id="c6_val" value="36" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=18;fontStyle=1;fontColor=#000000;fontFamily=Helvetica;" vertex="1" parent="1"><mxGeometry x="532" y="341" width="100" height="24" as="geometry" /></mxCell>
        <mxCell id="c6_sub" value="pihak/klien" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=9;fontColor=#666666;fontFamily=Helvetica;" vertex="1" parent="1"><mxGeometry x="532" y="367" width="140" height="14" as="geometry" /></mxCell>

        <!-- Card 7: Total Qty NG -->
        <mxCell id="card_7" value="" style="rounded=8;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;strokeWidth=1.2;" vertex="1" parent="1">
          <mxGeometry x="778" y="318" width="243" height="72" as="geometry" />
        </mxCell>
        <mxCell id="c7_dot" value="" style="ellipse;whiteSpace=wrap;html=1;aspect=fixed;fillColor=#000000;strokeColor=none;" vertex="1" parent="1"><mxGeometry x="791" y="328" width="7" height="7" as="geometry" /></mxCell>
        <mxCell id="c7_lbl" value="Total Qty NG" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontStyle=1;fontColor=#000000;fontFamily=Helvetica;" vertex="1" parent="1"><mxGeometry x="803" y="324" width="150" height="16" as="geometry" /></mxCell>
        <mxCell id="c7_val" value="63,671" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=18;fontStyle=1;fontColor=#000000;fontFamily=Helvetica;" vertex="1" parent="1"><mxGeometry x="791" y="341" width="130" height="24" as="geometry" /></mxCell>
        <mxCell id="c7_sub" value="pcs terdeteksi" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=9;fontColor=#666666;fontFamily=Helvetica;" vertex="1" parent="1"><mxGeometry x="791" y="367" width="140" height="14" as="geometry" /></mxCell>

        <!-- Card 8: Visit Aktif -->
        <mxCell id="card_8" value="" style="rounded=8;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;strokeWidth=1.2;" vertex="1" parent="1">
          <mxGeometry x="1037" y="318" width="243" height="72" as="geometry" />
        </mxCell>
        <mxCell id="c8_dot" value="" style="ellipse;whiteSpace=wrap;html=1;aspect=fixed;fillColor=#000000;strokeColor=none;" vertex="1" parent="1"><mxGeometry x="1050" y="328" width="7" height="7" as="geometry" /></mxCell>
        <mxCell id="c8_lbl" value="Visit Aktif" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontStyle=1;fontColor=#000000;fontFamily=Helvetica;" vertex="1" parent="1"><mxGeometry x="1062" y="324" width="150" height="16" as="geometry" /></mxCell>
        <mxCell id="c8_arrow" value="→" style="text;html=1;strokeColor=none;fillColor=none;align=right;verticalAlign=middle;fontSize=11;fontColor=#888888;" vertex="1" parent="1"><mxGeometry x="1252" y="324" width="18" height="16" as="geometry" /></mxCell>
        <mxCell id="c8_val" value="4" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=18;fontStyle=1;fontColor=#000000;fontFamily=Helvetica;" vertex="1" parent="1"><mxGeometry x="1050" y="341" width="100" height="24" as="geometry" /></mxCell>
        <mxCell id="c8_sub" value="agenda kunjungan" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=9;fontColor=#666666;fontFamily=Helvetica;" vertex="1" parent="1"><mxGeometry x="1050" y="367" width="140" height="14" as="geometry" /></mxCell>


        <!-- ==================== 3. JADWAL KUNJUNGAN LAPANGAN (TABEL) ==================== -->
        <mxCell id="visit_card" value="" style="rounded=8;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;strokeWidth=1.2;" vertex="1" parent="1">
          <mxGeometry x="260" y="404" width="1020" height="190" as="geometry" />
        </mxCell>
        <mxCell id="vc_icon" value="🚐" style="text;html=1;strokeColor=none;fillColor=none;align=center;verticalAlign=middle;fontSize=14;" vertex="1" parent="1">
          <mxGeometry x="276" y="414" width="20" height="20" as="geometry" />
        </mxCell>
        <mxCell id="vc_title" value="Jadwal Kunjungan Lapangan" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=13;fontStyle=1;fontColor=#000000;fontFamily=Helvetica;" vertex="1" parent="1">
          <mxGeometry x="302" y="414" width="200" height="20" as="geometry" />
        </mxCell>
        <mxCell id="vc_badge" value="4 aktif" style="rounded=10;whiteSpace=wrap;html=1;fillColor=#f0f0f0;strokeColor=#888888;strokeWidth=1;fontSize=9;fontColor=#000000;align=center;" vertex="1" parent="1">
          <mxGeometry x="490" y="415" width="50" height="18" as="geometry" />
        </mxCell>
        <mxCell id="vc_link" value="Lihat semua →" style="text;html=1;strokeColor=none;fillColor=none;align=right;verticalAlign=middle;fontSize=11;fontStyle=1;fontColor=#000000;fontFamily=Helvetica;" vertex="1" parent="1">
          <mxGeometry x="1155" y="414" width="105" height="20" as="geometry" />
        </mxCell>

        <!-- Garis Header Tabel -->
        <mxCell id="vc_line_th" value="" style="endArrow=none;html=1;strokeColor=#000000;strokeWidth=1;" edge="1" parent="1">
          <mxGeometry relative="1" as="geometry"><mxPoint x="260" y="442" as="sourcePoint"/><mxPoint x="1280" y="442" as="targetPoint"/></mxGeometry>
        </mxCell>

        <!-- Table Columns Header -->
        <mxCell id="th_bg" value="" style="rounded=0;whiteSpace=wrap;html=1;fillColor=#f8f8f8;strokeColor=none;" vertex="1" parent="1">
          <mxGeometry x="261" y="443" width="1018" height="24" as="geometry" />
        </mxCell>
        <mxCell id="th_col1" value="NO COMPLAINT" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=9;fontStyle=1;fontColor=#444444;" vertex="1" parent="1"><mxGeometry x="280" y="445" width="120" height="20" as="geometry" /></mxCell>
        <mxCell id="th_col2" value="CUSTOMER / PT" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=9;fontStyle=1;fontColor=#444444;" vertex="1" parent="1"><mxGeometry x="430" y="445" width="200" height="20" as="geometry" /></mxCell>
        <mxCell id="th_col3" value="TANGGAL VISIT" style="text;html=1;strokeColor=none;fillColor=none;align=center;verticalAlign=middle;fontSize=9;fontStyle=1;fontColor=#444444;" vertex="1" parent="1"><mxGeometry x="670" y="445" width="140" height="20" as="geometry" /></mxCell>
        <mxCell id="th_col4" value="STATUS / H-MINUS" style="text;html=1;strokeColor=none;fillColor=none;align=center;verticalAlign=middle;fontSize=9;fontStyle=1;fontColor=#444444;" vertex="1" parent="1"><mxGeometry x="835" y="445" width="120" height="20" as="geometry" /></mxCell>
        <mxCell id="th_col5" value="CATATAN KUNJUNGAN" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=9;fontStyle=1;fontColor=#444444;" vertex="1" parent="1"><mxGeometry x="980" y="445" width="280" height="20" as="geometry" /></mxCell>

        <!-- Row 1 -->
        <mxCell id="r1_line" value="" style="endArrow=none;html=1;strokeColor=#e0e0e0;strokeWidth=1;" edge="1" parent="1"><mxGeometry relative="1" as="geometry"><mxPoint x="260" y="467" as="sourcePoint"/><mxPoint x="1280" y="467" as="targetPoint"/></mxGeometry></mxCell>
        <mxCell id="tb_r1_c1" value="COMP-2026-004" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontFamily=Courier;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="280" y="470" width="120" height="24" as="geometry" /></mxCell>
        <mxCell id="tb_r1_c2" value="PT Astra Honda Motor" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="430" y="470" width="200" height="24" as="geometry" /></mxCell>
        <mxCell id="tb_r1_c3" value="12 Okt 2026 · 09:00" style="text;html=1;strokeColor=none;fillColor=none;align=center;verticalAlign=middle;fontSize=10;fontColor=#333333;" vertex="1" parent="1"><mxGeometry x="670" y="470" width="140" height="24" as="geometry" /></mxCell>
        <mxCell id="tb_r1_badge" value="[●] HARI INI" style="rounded=10;whiteSpace=wrap;html=1;fillColor=#000000;strokeColor=#000000;fontColor=#ffffff;fontSize=9;fontStyle=1;align=center;" vertex="1" parent="1"><mxGeometry x="860" y="474" width="70" height="18" as="geometry" /></mxCell>
        <mxCell id="tb_r1_c5" value="Verifikasi penanganan scratch part cover body" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontColor=#555555;" vertex="1" parent="1"><mxGeometry x="980" y="470" width="280" height="24" as="geometry" /></mxCell>

        <!-- Row 2 -->
        <mxCell id="r2_line" value="" style="endArrow=none;html=1;strokeColor=#e0e0e0;strokeWidth=1;" edge="1" parent="1"><mxGeometry relative="1" as="geometry"><mxPoint x="260" y="499" as="sourcePoint"/><mxPoint x="1280" y="499" as="targetPoint"/></mxGeometry></mxCell>
        <mxCell id="tb_r2_c1" value="COMP-2026-002" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontFamily=Courier;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="280" y="502" width="120" height="24" as="geometry" /></mxCell>
        <mxCell id="tb_r2_c2" value="PT Yamaha Indonesia Motor" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="430" y="502" width="200" height="24" as="geometry" /></mxCell>
        <mxCell id="tb_r2_c3" value="16 Okt 2026 · 13:30" style="text;html=1;strokeColor=none;fillColor=none;align=center;verticalAlign=middle;fontSize=10;fontColor=#333333;" vertex="1" parent="1"><mxGeometry x="670" y="502" width="140" height="24" as="geometry" /></mxCell>
        <mxCell id="tb_r2_badge" value="H-4" style="rounded=10;whiteSpace=wrap;html=1;fillColor=#666666;strokeColor=#000000;fontColor=#ffffff;fontSize=9;fontStyle=1;align=center;" vertex="1" parent="1"><mxGeometry x="870" y="506" width="50" height="18" as="geometry" /></mxCell>
        <mxCell id="tb_r2_c5" value="Pengecekan preventive maintenance mesin press" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontColor=#555555;" vertex="1" parent="1"><mxGeometry x="980" y="502" width="280" height="24" as="geometry" /></mxCell>

        <!-- Row 3 -->
        <mxCell id="r3_line" value="" style="endArrow=none;html=1;strokeColor=#e0e0e0;strokeWidth=1;" edge="1" parent="1"><mxGeometry relative="1" as="geometry"><mxPoint x="260" y="531" as="sourcePoint"/><mxPoint x="1280" y="531" as="targetPoint"/></mxGeometry></mxCell>
        <mxCell id="tb_r3_c1" value="COMP-2026-001" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontFamily=Courier;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="280" y="534" width="120" height="24" as="geometry" /></mxCell>
        <mxCell id="tb_r3_c2" value="PT Denso Indonesia" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="430" y="534" width="200" height="24" as="geometry" /></mxCell>
        <mxCell id="tb_r3_c3" value="20 Okt 2026 · 10:00" style="text;html=1;strokeColor=none;fillColor=none;align=center;verticalAlign=middle;fontSize=10;fontColor=#333333;" vertex="1" parent="1"><mxGeometry x="670" y="534" width="140" height="24" as="geometry" /></mxCell>
        <mxCell id="tb_r3_badge" value="H-8" style="rounded=10;whiteSpace=wrap;html=1;fillColor=#e5e5e5;strokeColor=#888888;fontColor=#000000;fontSize=9;fontStyle=1;align=center;" vertex="1" parent="1"><mxGeometry x="870" y="538" width="50" height="18" as="geometry" /></mxCell>
        <mxCell id="tb_r3_c5" value="Audit berkala toleransi dimensi part bracket" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontColor=#555555;" vertex="1" parent="1"><mxGeometry x="980" y="534" width="280" height="24" as="geometry" /></mxCell>


        <!-- ==============================================================
             BARIS 4 — ANALISIS QC: PARETO + FISHBONE + APRIORI
             (SESUAI SISTEM NYATA: PARETO ATAS FULL-WIDTH, LALU DIVIDER, LALU APRIORI BAWAH FULL-WIDTH)
        ============================================================== -->
        <mxCell id="analysis_card" value="" style="rounded=8;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;strokeWidth=1.2;" vertex="1" parent="1">
          <mxGeometry x="260" y="606" width="1020" height="645" as="geometry" />
        </mxCell>
        <mxCell id="an_icon" value="🔬" style="text;html=1;strokeColor=none;fillColor=none;align=center;verticalAlign=middle;fontSize=14;" vertex="1" parent="1">
          <mxGeometry x="276" y="616" width="20" height="20" as="geometry" />
        </mxCell>
        <mxCell id="an_title" value="Analisis Kualitas — Seven Tools &amp; Apriori" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=13;fontStyle=1;fontColor=#000000;fontFamily=Helvetica;" vertex="1" parent="1">
          <mxGeometry x="302" y="616" width="280" height="20" as="geometry" />
        </mxCell>
        <mxCell id="an_badge" value="Insight Otomatis" style="rounded=10;whiteSpace=wrap;html=1;fillColor=#f0f0f0;strokeColor=#888888;strokeWidth=1;fontSize=9;fontColor=#000000;align=center;" vertex="1" parent="1">
          <mxGeometry x="585" y="617" width="95" height="18" as="geometry" />
        </mxCell>
        <mxCell id="an_link" value="Lihat Seven Tools lengkap →" style="text;html=1;strokeColor=none;fillColor=none;align=right;verticalAlign=middle;fontSize=11;fontStyle=1;fontColor=#000000;fontFamily=Helvetica;" vertex="1" parent="1">
          <mxGeometry x="1075" y="616" width="185" height="20" as="geometry" />
        </mxCell>

        <!-- Garis Pemisah Header Analisis -->
        <mxCell id="an_line_h" value="" style="endArrow=none;html=1;strokeColor=#000000;strokeWidth=1;" edge="1" parent="1">
          <mxGeometry relative="1" as="geometry"><mxPoint x="260" y="642" as="sourcePoint"/><mxPoint x="1280" y="642" as="targetPoint"/></mxGeometry>
        </mxCell>

        <!-- Subtitle Alur Baca Analisis -->
        <mxCell id="an_subtitle" value="Alur baca: Pareto (defect apa yang paling banyak?) → Apriori (pola apa yang sering muncul bersamaan?). Untuk analisis Fishbone 6M lengkap, lihat halaman Seven Tools." style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontColor=#555555;fontFamily=Helvetica;" vertex="1" parent="1">
          <mxGeometry x="276" y="648" width="980" height="18" as="geometry" />
        </mxCell>


        <!-- ── BAGIAN ATAS: DIAGRAM PARETO (FULL WIDTH & PROPORSIONAL) ── -->
        <mxCell id="box_pareto_fw" value="" style="rounded=8;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#cccccc;strokeWidth=1;" vertex="1" parent="1">
          <mxGeometry x="276" y="672" width="988" height="275" as="geometry" />
        </mxCell>

        <!-- Tab Bar Pareto Buttons (Full Width Navigation Tabs) -->
        <mxCell id="tab_bg_p" value="" style="rounded=0;whiteSpace=wrap;html=1;fillColor=#f8f8f8;strokeColor=#e0e0e0;strokeWidth=1;" vertex="1" parent="1">
          <mxGeometry x="276" y="672" width="988" height="34" as="geometry" />
        </mxCell>
        <!-- Tab 1 Active: Ketidaksesuaian -->
        <mxCell id="tab_btn_p1" value="📊 Ketidaksesuaian" style="rounded=0;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;strokeWidth=1.5;fontColor=#000000;fontSize=10;fontStyle=1;align=center;" vertex="1" parent="1">
          <mxGeometry x="276" y="672" width="145" height="34" as="geometry" />
        </mxCell>
        <!-- Tab 2: Detail KS -->
        <mxCell id="tab_btn_p2" value="🔍 Detail KS" style="rounded=0;whiteSpace=wrap;html=1;fillColor=none;strokeColor=none;fontColor=#555555;fontSize=10;align=center;" vertex="1" parent="1">
          <mxGeometry x="421" y="672" width="115" height="34" as="geometry" />
        </mxCell>
        <!-- Tab 3: Customer -->
        <mxCell id="tab_btn_p3" value="🏢 Customer" style="rounded=0;whiteSpace=wrap;html=1;fillColor=none;strokeColor=none;fontColor=#555555;fontSize=10;align=center;" vertex="1" parent="1">
          <mxGeometry x="536" y="672" width="115" height="34" as="geometry" />
        </mxCell>
        <!-- Tab 4: Penyebab -->
        <mxCell id="tab_btn_p4" value="⚠️ Penyebab" style="rounded=0;whiteSpace=wrap;html=1;fillColor=none;strokeColor=none;fontColor=#555555;fontSize=10;align=center;" vertex="1" parent="1">
          <mxGeometry x="651" y="672" width="115" height="34" as="geometry" />
        </mxCell>

        <!-- Keterangan Panel Pareto Aktif -->
        <mxCell id="lbl_p_desc" value="Jenis Ketidaksesuaian — frekuensi dari yang terbanyak (aturan 80/20)." style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=9;fontColor=#666666;fontFamily=Helvetica;" vertex="1" parent="1">
          <mxGeometry x="290" y="712" width="480" height="16" as="geometry" />
        </mxCell>
        <mxCell id="lbl_p_legend" value="[■] Frekuensi Kasus Defect     [---] Persentase Kumulatif (Garis Pareto 80%)" style="text;html=1;strokeColor=none;fillColor=none;align=right;verticalAlign=middle;fontSize=9;fontColor=#444444;fontFamily=Helvetica;" vertex="1" parent="1">
          <mxGeometry x="815" y="712" width="430" height="16" as="geometry" />
        </mxCell>

        <!-- Pareto Chart Canvas Area (Full Width Grid & Axis) -->
        <mxCell id="p_axis_y" value="" style="endArrow=none;html=1;strokeColor=#000000;strokeWidth=1;" edge="1" parent="1"><mxGeometry relative="1" as="geometry"><mxPoint x="325" y="736" as="sourcePoint"/><mxPoint x="325" y="910" as="targetPoint"/></mxGeometry></mxCell>
        <mxCell id="p_axis_y_right" value="" style="endArrow=none;html=1;strokeColor=#888888;strokeWidth=1;dashed=1;" edge="1" parent="1"><mxGeometry relative="1" as="geometry"><mxPoint x="1235" y="736" as="sourcePoint"/><mxPoint x="1235" y="910" as="targetPoint"/></mxGeometry></mxCell>
        <mxCell id="p_axis_x" value="" style="endArrow=none;html=1;strokeColor=#000000;strokeWidth=1;" edge="1" parent="1"><mxGeometry relative="1" as="geometry"><mxPoint x="325" y="910" as="sourcePoint"/><mxPoint x="1235" y="910" as="targetPoint"/></mxGeometry></mxCell>
        <!-- Grid lines -->
        <mxCell id="p_grid_1" value="" style="endArrow=none;html=1;strokeColor=#eeeeee;strokeWidth=1;dashed=1;" edge="1" parent="1"><mxGeometry relative="1" as="geometry"><mxPoint x="325" y="770" as="sourcePoint"/><mxPoint x="1235" y="770" as="targetPoint"/></mxGeometry></mxCell>
        <mxCell id="p_grid_2" value="" style="endArrow=none;html=1;strokeColor=#eeeeee;strokeWidth=1;dashed=1;" edge="1" parent="1"><mxGeometry relative="1" as="geometry"><mxPoint x="325" y="817" as="sourcePoint"/><mxPoint x="1235" y="817" as="targetPoint"/></mxGeometry></mxCell>
        <mxCell id="p_grid_3" value="" style="endArrow=none;html=1;strokeColor=#eeeeee;strokeWidth=1;dashed=1;" edge="1" parent="1"><mxGeometry relative="1" as="geometry"><mxPoint x="325" y="864" as="sourcePoint"/><mxPoint x="1235" y="864" as="targetPoint"/></mxGeometry></mxCell>

        <!-- Sumbu Y Values (Kiri: Jumlah Kasus) -->
        <mxCell id="lbl_y_50" value="50" style="text;html=1;strokeColor=none;fillColor=none;align=right;fontSize=8;fontColor=#666666;" vertex="1" parent="1"><mxGeometry x="295" y="746" width="25" height="12" as="geometry" /></mxCell>
        <mxCell id="lbl_y_35" value="35" style="text;html=1;strokeColor=none;fillColor=none;align=right;fontSize=8;fontColor=#666666;" vertex="1" parent="1"><mxGeometry x="295" y="793" width="25" height="12" as="geometry" /></mxCell>
        <mxCell id="lbl_y_20" value="20" style="text;html=1;strokeColor=none;fillColor=none;align=right;fontSize=8;fontColor=#666666;" vertex="1" parent="1"><mxGeometry x="295" y="840" width="25" height="12" as="geometry" /></mxCell>
        <mxCell id="lbl_y_0" value="0" style="text;html=1;strokeColor=none;fillColor=none;align=right;fontSize=8;fontColor=#666666;" vertex="1" parent="1"><mxGeometry x="295" y="904" width="25" height="12" as="geometry" /></mxCell>

        <!-- Sumbu Y Values (Kanan: Persen Kumulatif) -->
        <mxCell id="lbl_yr_100" value="100%" style="text;html=1;strokeColor=none;fillColor=none;align=left;fontSize=8;fontColor=#666666;" vertex="1" parent="1"><mxGeometry x="1240" y="746" width="30" height="12" as="geometry" /></mxCell>
        <mxCell id="lbl_yr_80" value="80%" style="text;html=1;strokeColor=none;fillColor=none;align=left;fontSize=8;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="1240" y="776" width="30" height="12" as="geometry" /></mxCell>
        <mxCell id="lbl_yr_50" value="50%" style="text;html=1;strokeColor=none;fillColor=none;align=left;fontSize=8;fontColor=#666666;" vertex="1" parent="1"><mxGeometry x="1240" y="825" width="30" height="12" as="geometry" /></mxCell>
        <mxCell id="lbl_yr_0" value="0%" style="text;html=1;strokeColor=none;fillColor=none;align=left;fontSize=8;fontColor=#666666;" vertex="1" parent="1"><mxGeometry x="1240" y="904" width="30" height="12" as="geometry" /></mxCell>

        <!-- 6 Pareto Bars (Proporsional & Lebar) -->
        <!-- Bar 1: Scratch -->
        <mxCell id="bar_1" value="48" style="rounded=2;whiteSpace=wrap;html=1;fillColor=#000000;strokeColor=#000000;fontColor=#ffffff;fontSize=9;fontStyle=1;align=center;verticalAlign=top;spacingTop=2;" vertex="1" parent="1"><mxGeometry x="365" y="756" width="70" height="154" as="geometry" /></mxCell>
        <mxCell id="lbl_b1" value="Scratch" style="text;html=1;strokeColor=none;fillColor=none;align=center;fontSize=9;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="360" y="914" width="80" height="14" as="geometry" /></mxCell>

        <!-- Bar 2: Dent -->
        <mxCell id="bar_2" value="32" style="rounded=2;whiteSpace=wrap;html=1;fillColor=#333333;strokeColor=#000000;fontColor=#ffffff;fontSize=9;fontStyle=1;align=center;verticalAlign=top;spacingTop=2;" vertex="1" parent="1"><mxGeometry x="510" y="808" width="70" height="102" as="geometry" /></mxCell>
        <mxCell id="lbl_b2" value="Dent" style="text;html=1;strokeColor=none;fillColor=none;align=center;fontSize=9;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="505" y="914" width="80" height="14" as="geometry" /></mxCell>

        <!-- Bar 3: Dimensi NG -->
        <mxCell id="bar_3" value="20" style="rounded=2;whiteSpace=wrap;html=1;fillColor=#666666;strokeColor=#000000;fontColor=#ffffff;fontSize=9;fontStyle=1;align=center;verticalAlign=top;spacingTop=2;" vertex="1" parent="1"><mxGeometry x="655" y="846" width="70" height="64" as="geometry" /></mxCell>
        <mxCell id="lbl_b3" value="Dimensi NG" style="text;html=1;strokeColor=none;fillColor=none;align=center;fontSize=9;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="650" y="914" width="80" height="14" as="geometry" /></mxCell>

        <!-- Bar 4: Karat / Korosi -->
        <mxCell id="bar_4" value="12" style="rounded=2;whiteSpace=wrap;html=1;fillColor=#888888;strokeColor=#000000;fontColor=#ffffff;fontSize=9;fontStyle=1;align=center;verticalAlign=top;spacingTop=2;" vertex="1" parent="1"><mxGeometry x="800" y="872" width="70" height="38" as="geometry" /></mxCell>
        <mxCell id="lbl_b4" value="Karat / Korosi" style="text;html=1;strokeColor=none;fillColor=none;align=center;fontSize=9;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="795" y="914" width="80" height="14" as="geometry" /></mxCell>

        <!-- Bar 5: Gagal Rekat -->
        <mxCell id="bar_5" value="8" style="rounded=2;whiteSpace=wrap;html=1;fillColor=#aaaaaa;strokeColor=#000000;fontColor=#000000;fontSize=9;fontStyle=1;align=center;verticalAlign=top;spacingTop=2;" vertex="1" parent="1"><mxGeometry x="945" y="885" width="70" height="25" as="geometry" /></mxCell>
        <mxCell id="lbl_b5" value="Gagal Rekat" style="text;html=1;strokeColor=none;fillColor=none;align=center;fontSize=9;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="940" y="914" width="80" height="14" as="geometry" /></mxCell>

        <!-- Bar 6: Lainnya -->
        <mxCell id="bar_6" value="5" style="rounded=2;whiteSpace=wrap;html=1;fillColor=#cccccc;strokeColor=#000000;fontColor=#000000;fontSize=9;fontStyle=1;align=center;verticalAlign=top;spacingTop=2;" vertex="1" parent="1"><mxGeometry x="1090" y="894" width="70" height="16" as="geometry" /></mxCell>
        <mxCell id="lbl_b6" value="Lainnya" style="text;html=1;strokeColor=none;fillColor=none;align=center;fontSize=9;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="1085" y="914" width="80" height="14" as="geometry" /></mxCell>

        <!-- Garis Kumulatif 80% (Pareto Polyline) -->
        <mxCell id="p_line_cum" value="" style="edgeStyle=none;endArrow=none;html=1;strokeColor=#000000;strokeWidth=2;dashed=1;" edge="1" parent="1">
          <mxGeometry relative="1" as="geometry">
            <mxPoint x="400" y="828" as="sourcePoint" />
            <mxPoint x="1125" y="746" as="targetPoint" />
            <Array as="points">
              <mxPoint x="545" y="785" />
              <mxPoint x="690" y="765" />
              <mxPoint x="835" y="753" />
              <mxPoint x="980" y="748" />
            </Array>
          </mxGeometry>
        </mxCell>
        <!-- 80% Cutoff Line -->
        <mxCell id="p_cutoff_80" value="" style="endArrow=none;html=1;strokeColor=#666666;strokeWidth=1;dashed=1;" edge="1" parent="1">
          <mxGeometry relative="1" as="geometry"><mxPoint x="325" y="780" as="sourcePoint"/><mxPoint x="1235" y="780" as="targetPoint"/></mxGeometry>
        </mxCell>
        <mxCell id="p_cutoff_tag" value="Cutoff 80%" style="text;html=1;strokeColor=none;fillColor=none;align=left;fontSize=8;fontStyle=1;fontColor=#444444;" vertex="1" parent="1"><mxGeometry x="330" y="768" width="80" height="12" as="geometry" /></mxCell>


        <!-- ── GARIS PEMISAH ANTARA PARETO DAN APRIORI ── -->
        <mxCell id="div_pareto_apriori" value="" style="endArrow=none;html=1;strokeColor=#e0e0e0;strokeWidth=1.5;" edge="1" parent="1">
          <mxGeometry relative="1" as="geometry"><mxPoint x="276" y="960" as="sourcePoint"/><mxPoint x="1264" y="960" as="targetPoint"/></mxGeometry>
        </mxCell>


        <!-- ── BAGIAN BAWAH: GRAFIK POLA KETERKAITAN CACAT (APRIORI LIFT RATIO) ── -->
        <mxCell id="box_ap_header_title" value="📊 Grafik Pola Keterkaitan Cacat (Lift Ratio — Apriori)" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=12;fontStyle=1;fontColor=#000000;fontFamily=Helvetica;" vertex="1" parent="1">
          <mxGeometry x="276" y="972" width="400" height="20" as="geometry" />
        </mxCell>
        <mxCell id="box_ap_header_sub" value="Tingkat kekuatan kaitan sebab-akibat antara jenis ketidaksesuaian dan penyebab utama." style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontColor=#666666;fontFamily=Helvetica;" vertex="1" parent="1">
          <mxGeometry x="276" y="992" width="550" height="16" as="geometry" />
        </mxCell>
        <mxCell id="btn_ap_link" value="Analisis lengkap →" style="text;html=1;strokeColor=none;fillColor=none;align=right;verticalAlign=middle;fontSize=11;fontStyle=1;fontColor=#000000;fontFamily=Helvetica;" vertex="1" parent="1">
          <mxGeometry x="1140" y="974" width="120" height="20" as="geometry" />
        </mxCell>

        <!-- Apriori Chart Canvas Box (Horizontal Rule Ranking) -->
        <mxCell id="box_ap_chart" value="" style="rounded=8;whiteSpace=wrap;html=1;fillColor=#fafafa;strokeColor=#e0e0e0;strokeWidth=1;" vertex="1" parent="1">
          <mxGeometry x="276" y="1014" width="988" height="195" as="geometry" />
        </mxCell>

        <!-- Rule 1: Scratch -> Dies Aus -->
        <mxCell id="r1_rule_lbl" value="[Scratch] ➔ [Dies Aus / Mesin Press]" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="292" y="1022" width="280" height="16" as="geometry" /></mxCell>
        <mxCell id="r1_rule_supp" value="Support: 18.5% | Conf: 85.2%" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=9;fontColor=#666666;" vertex="1" parent="1"><mxGeometry x="580" y="1022" width="160" height="16" as="geometry" /></mxCell>
        <mxCell id="r1_bar_bg" value="" style="rounded=3;whiteSpace=wrap;html=1;fillColor=#eeeeee;strokeColor=none;" vertex="1" parent="1"><mxGeometry x="292" y="1040" width="810" height="10" as="geometry" /></mxCell>
        <mxCell id="r1_bar_val" value="" style="rounded=3;whiteSpace=wrap;html=1;fillColor=#000000;strokeColor=none;" vertex="1" parent="1"><mxGeometry x="292" y="1040" width="715" height="10" as="geometry" /></mxCell>
        <mxCell id="r1_lift_val" value="Lift: 5.4x (Sangat Kuat)" style="text;html=1;strokeColor=none;fillColor=none;align=right;verticalAlign=middle;fontSize=9.5;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="1110" y="1029" width="140" height="16" as="geometry" /></mxCell>

        <!-- Rule 2: Dent -> Handling Forklift -->
        <mxCell id="r2_rule_lbl" value="[Dent] ➔ [Handling Forklift / Operator]" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="292" y="1060" width="280" height="16" as="geometry" /></mxCell>
        <mxCell id="r2_rule_supp" value="Support: 12.3% | Conf: 71.4%" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=9;fontColor=#666666;" vertex="1" parent="1"><mxGeometry x="580" y="1060" width="160" height="16" as="geometry" /></mxCell>
        <mxCell id="r2_bar_bg" value="" style="rounded=3;whiteSpace=wrap;html=1;fillColor=#eeeeee;strokeColor=none;" vertex="1" parent="1"><mxGeometry x="292" y="1078" width="810" height="10" as="geometry" /></mxCell>
        <mxCell id="r2_bar_val" value="" style="rounded=3;whiteSpace=wrap;html=1;fillColor=#444444;strokeColor=none;" vertex="1" parent="1"><mxGeometry x="292" y="1078" width="510" height="10" as="geometry" /></mxCell>
        <mxCell id="r2_lift_val" value="Lift: 3.8x (Kuat)" style="text;html=1;strokeColor=none;fillColor=none;align=right;verticalAlign=middle;fontSize=9.5;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="1110" y="1067" width="140" height="16" as="geometry" /></mxCell>

        <!-- Rule 3: Dimensi NG -> Setting Parameter Mesin -->
        <mxCell id="r3_rule_lbl" value="[Dimensi NG] ➔ [Setting Parameter Mesin]" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="292" y="1098" width="280" height="16" as="geometry" /></mxCell>
        <mxCell id="r3_rule_supp" value="Support: 8.6% | Conf: 58.0%" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=9;fontColor=#666666;" vertex="1" parent="1"><mxGeometry x="580" y="1098" width="160" height="16" as="geometry" /></mxCell>
        <mxCell id="r3_bar_bg" value="" style="rounded=3;whiteSpace=wrap;html=1;fillColor=#eeeeee;strokeColor=none;" vertex="1" parent="1"><mxGeometry x="292" y="1116" width="810" height="10" as="geometry" /></mxCell>
        <mxCell id="r3_bar_val" value="" style="rounded=3;whiteSpace=wrap;html=1;fillColor=#777777;strokeColor=none;" vertex="1" parent="1"><mxGeometry x="292" y="1116" width="340" height="10" as="geometry" /></mxCell>
        <mxCell id="r3_lift_val" value="Lift: 2.5x (Sedang)" style="text;html=1;strokeColor=none;fillColor=none;align=right;verticalAlign=middle;fontSize=9.5;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="1110" y="1105" width="140" height="16" as="geometry" /></mxCell>

        <!-- Rule 4: Karat / Korosi -> Kelembaban Gudang -->
        <mxCell id="r4_rule_lbl" value="[Karat / Korosi] ➔ [Kelembaban Gudang / Cuaca]" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="292" y="1136" width="280" height="16" as="geometry" /></mxCell>
        <mxCell id="r4_rule_supp" value="Support: 6.2% | Conf: 51.5%" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=9;fontColor=#666666;" vertex="1" parent="1"><mxGeometry x="580" y="1136" width="160" height="16" as="geometry" /></mxCell>
        <mxCell id="r4_bar_bg" value="" style="rounded=3;whiteSpace=wrap;html=1;fillColor=#eeeeee;strokeColor=none;" vertex="1" parent="1"><mxGeometry x="292" y="1154" width="810" height="10" as="geometry" /></mxCell>
        <mxCell id="r4_bar_val" value="" style="rounded=3;whiteSpace=wrap;html=1;fillColor=#999999;strokeColor=none;" vertex="1" parent="1"><mxGeometry x="292" y="1154" width="285" height="10" as="geometry" /></mxCell>
        <mxCell id="r4_lift_val" value="Lift: 2.1x (Sedang)" style="text;html=1;strokeColor=none;fillColor=none;align=right;verticalAlign=middle;fontSize=9.5;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="1110" y="1143" width="140" height="16" as="geometry" /></mxCell>

        <!-- Apriori Footer Metadata & Legend -->
        <mxCell id="ap_footer_txt" value="Berdasarkan 182 transaksi komplain · Terurut dari kaitan terkuat" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=9.5;fontColor=#666666;" vertex="1" parent="1">
          <mxGeometry x="292" y="1178" width="400" height="18" as="geometry" />
        </mxCell>
        <mxCell id="ap_footer_legend" value="[■] Sangat Kuat (&amp;#8805;5x)     [■] Kuat (&amp;#8805;3x)     [■] Sedang (&amp;#8805;1.5x)" style="text;html=1;strokeColor=none;fillColor=none;align=right;verticalAlign=middle;fontSize=9.5;fontStyle=1;fontColor=#333333;" vertex="1" parent="1">
          <mxGeometry x="840" y="1178" width="410" height="18" as="geometry" />
        </mxCell>


        <!-- ==============================================================
             BARIS 5 — MINI LAPORAN VISUAL 2026
        ============================================================== -->
        <mxCell id="lv_card" value="" style="rounded=8;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;strokeWidth=1.2;" vertex="1" parent="1">
          <mxGeometry x="260" y="1268" width="1020" height="520" as="geometry" />
        </mxCell>
        <mxCell id="lv_icon" value="📋" style="text;html=1;strokeColor=none;fillColor=none;align=center;verticalAlign=middle;fontSize=14;" vertex="1" parent="1">
          <mxGeometry x="276" y="1278" width="20" height="20" as="geometry" />
        </mxCell>
        <mxCell id="lv_title" value="Laporan Visual 2026" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=13;fontStyle=1;fontColor=#000000;fontFamily=Helvetica;" vertex="1" parent="1">
          <mxGeometry x="302" y="1278" width="170" height="20" as="geometry" />
        </mxCell>
        <mxCell id="lv_badge" value="Ringkasan Tahun Berjalan" style="rounded=10;whiteSpace=wrap;html=1;fillColor=#f0f0f0;strokeColor=#888888;strokeWidth=1;fontSize=9;fontColor=#000000;align=center;" vertex="1" parent="1">
          <mxGeometry x="475" y="1279" width="145" height="18" as="geometry" />
        </mxCell>
        <mxCell id="lv_link" value="Laporan lengkap →" style="text;html=1;strokeColor=none;fillColor=none;align=right;verticalAlign=middle;fontSize=11;fontStyle=1;fontColor=#000000;fontFamily=Helvetica;" vertex="1" parent="1">
          <mxGeometry x="1135" y="1278" width="125" height="20" as="geometry" />
        </mxCell>

        <!-- Garis Pemisah Header Laporan Visual -->
        <mxCell id="lv_line_h" value="" style="endArrow=none;html=1;strokeColor=#000000;strokeWidth=1;" edge="1" parent="1">
          <mxGeometry relative="1" as="geometry"><mxPoint x="260" y="1306" as="sourcePoint"/><mxPoint x="1280" y="1306" as="targetPoint"/></mxGeometry>
        </mxCell>

        <!-- 5 MINI KPI WIDGETS (w=188 each, gap=15) -->
        <mxCell id="mk_1_bg" value="" style="rounded=6;whiteSpace=wrap;html=1;fillColor=#fafafa;strokeColor=#e0e0e0;strokeWidth=1;" vertex="1" parent="1"><mxGeometry x="275" y="1316" width="188" height="52" as="geometry" /></mxCell>
        <mxCell id="mk_1_val" value="182" style="text;html=1;strokeColor=none;fillColor=none;align=center;verticalAlign=middle;fontSize=16;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="275" y="1318" width="188" height="24" as="geometry" /></mxCell>
        <mxCell id="mk_1_lbl" value="● Total Complaint" style="text;html=1;strokeColor=none;fillColor=none;align=center;verticalAlign=middle;fontSize=9;fontColor=#555555;" vertex="1" parent="1"><mxGeometry x="275" y="1342" width="188" height="18" as="geometry" /></mxCell>

        <mxCell id="mk_2_bg" value="" style="rounded=6;whiteSpace=wrap;html=1;fillColor=#fafafa;strokeColor=#e0e0e0;strokeWidth=1;" vertex="1" parent="1"><mxGeometry x="478" y="1316" width="188" height="52" as="geometry" /></mxCell>
        <mxCell id="mk_2_val" value="1" style="text;html=1;strokeColor=none;fillColor=none;align=center;verticalAlign=middle;fontSize=16;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="478" y="1318" width="188" height="24" as="geometry" /></mxCell>
        <mxCell id="mk_2_lbl" value="● Status Open" style="text;html=1;strokeColor=none;fillColor=none;align=center;verticalAlign=middle;fontSize=9;fontColor=#555555;" vertex="1" parent="1"><mxGeometry x="478" y="1342" width="188" height="18" as="geometry" /></mxCell>

        <mxCell id="mk_3_bg" value="" style="rounded=6;whiteSpace=wrap;html=1;fillColor=#fafafa;strokeColor=#e0e0e0;strokeWidth=1;" vertex="1" parent="1"><mxGeometry x="681" y="1316" width="188" height="52" as="geometry" /></mxCell>
        <mxCell id="mk_3_val" value="1" style="text;html=1;strokeColor=none;fillColor=none;align=center;verticalAlign=middle;fontSize=16;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="681" y="1318" width="188" height="24" as="geometry" /></mxCell>
        <mxCell id="mk_3_lbl" value="● Status Diproses" style="text;html=1;strokeColor=none;fillColor=none;align=center;verticalAlign=middle;fontSize=9;fontColor=#555555;" vertex="1" parent="1"><mxGeometry x="681" y="1342" width="188" height="18" as="geometry" /></mxCell>

        <mxCell id="mk_4_bg" value="" style="rounded=6;whiteSpace=wrap;html=1;fillColor=#fafafa;strokeColor=#e0e0e0;strokeWidth=1;" vertex="1" parent="1"><mxGeometry x="884" y="1316" width="188" height="52" as="geometry" /></mxCell>
        <mxCell id="mk_4_val" value="180" style="text;html=1;strokeColor=none;fillColor=none;align=center;verticalAlign=middle;fontSize=16;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="884" y="1318" width="188" height="24" as="geometry" /></mxCell>
        <mxCell id="mk_4_lbl" value="● Status Close" style="text;html=1;strokeColor=none;fillColor=none;align=center;verticalAlign=middle;fontSize=9;fontColor=#555555;" vertex="1" parent="1"><mxGeometry x="884" y="1342" width="188" height="18" as="geometry" /></mxCell>

        <mxCell id="mk_5_bg" value="" style="rounded=6;whiteSpace=wrap;html=1;fillColor=#fafafa;strokeColor=#e0e0e0;strokeWidth=1;" vertex="1" parent="1"><mxGeometry x="1087" y="1316" width="178" height="52" as="geometry" /></mxCell>
        <mxCell id="mk_5_val" value="63,671" style="text;html=1;strokeColor=none;fillColor=none;align=center;verticalAlign=middle;fontSize=16;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="1087" y="1318" width="178" height="24" as="geometry" /></mxCell>
        <mxCell id="mk_5_lbl" value="● Qty NG (pcs)" style="text;html=1;strokeColor=none;fillColor=none;align=center;verticalAlign=middle;fontSize=9;fontColor=#555555;" vertex="1" parent="1"><mxGeometry x="1087" y="1342" width="178" height="18" as="geometry" /></mxCell>

        <!-- KIRI: GRAFIK TREND BULANAN 2026 (w=660, x=275) -->
        <mxCell id="box_trend_month" value="" style="rounded=6;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#cccccc;strokeWidth=1;" vertex="1" parent="1">
          <mxGeometry x="275" y="1380" width="660" height="210" as="geometry" />
        </mxCell>
        <mxCell id="lbl_tr_title" value="📈 TREND BULANAN 2026" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontStyle=1;fontColor=#444444;" vertex="1" parent="1">
          <mxGeometry x="290" y="1388" width="250" height="18" as="geometry" />
        </mxCell>

        <mxCell id="tm_axis_y" value="" style="endArrow=none;html=1;strokeColor=#000000;strokeWidth=1;" edge="1" parent="1"><mxGeometry relative="1" as="geometry"><mxPoint x="320" y="1417" as="sourcePoint"/><mxPoint x="320" y="1552" as="targetPoint"/></mxGeometry></mxCell>
        <mxCell id="tm_axis_x" value="" style="endArrow=none;html=1;strokeColor=#000000;strokeWidth=1;" edge="1" parent="1"><mxGeometry relative="1" as="geometry"><mxPoint x="320" y="1552" as="sourcePoint"/><mxPoint x="915" y="1552" as="targetPoint"/></mxGeometry></mxCell>
        <mxCell id="tm_grid_1" value="" style="endArrow=none;html=1;strokeColor=#eeeeee;strokeWidth=1;dashed=1;" edge="1" parent="1"><mxGeometry relative="1" as="geometry"><mxPoint x="320" y="1457" as="sourcePoint"/><mxPoint x="915" y="1457" as="targetPoint"/></mxGeometry></mxCell>
        <mxCell id="tm_grid_2" value="" style="endArrow=none;html=1;strokeColor=#eeeeee;strokeWidth=1;dashed=1;" edge="1" parent="1"><mxGeometry relative="1" as="geometry"><mxPoint x="320" y="1502" as="sourcePoint"/><mxPoint x="915" y="1502" as="targetPoint"/></mxGeometry></mxCell>

        <!-- Garis Trend Polyline -->
        <mxCell id="tm_line" value="" style="edgeStyle=none;endArrow=none;html=1;strokeColor=#000000;strokeWidth=2;" edge="1" parent="1">
          <mxGeometry relative="1" as="geometry">
            <mxPoint x="345" y="1532" as="sourcePoint" />
            <mxPoint x="890" y="1477" as="targetPoint" />
            <Array as="points">
              <mxPoint x="390" y="1502" /><mxPoint x="435" y="1467" /><mxPoint x="485" y="1517" />
              <mxPoint x="535" y="1452" /><mxPoint x="585" y="1492" /><mxPoint x="635" y="1442" />
              <mxPoint x="685" y="1507" /><mxPoint x="735" y="1462" /><mxPoint x="780" y="1522" />
              <mxPoint x="830" y="1462" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- Labels Bulan -->
        <mxCell id="tb_m1" value="Jan" style="text;html=1;strokeColor=none;fillColor=none;align=center;fontSize=8;fontColor=#444444;" vertex="1" parent="1"><mxGeometry x="335" y="1556" width="22" height="12" as="geometry" /></mxCell>
        <mxCell id="tb_m2" value="Feb" style="text;html=1;strokeColor=none;fillColor=none;align=center;fontSize=8;fontColor=#444444;" vertex="1" parent="1"><mxGeometry x="380" y="1556" width="22" height="12" as="geometry" /></mxCell>
        <mxCell id="tb_m3" value="Mar" style="text;html=1;strokeColor=none;fillColor=none;align=center;fontSize=8;fontColor=#444444;" vertex="1" parent="1"><mxGeometry x="425" y="1556" width="22" height="12" as="geometry" /></mxCell>
        <mxCell id="tb_m4" value="Apr" style="text;html=1;strokeColor=none;fillColor=none;align=center;fontSize=8;fontColor=#444444;" vertex="1" parent="1"><mxGeometry x="475" y="1556" width="22" height="12" as="geometry" /></mxCell>
        <mxCell id="tb_m5" value="Mei" style="text;html=1;strokeColor=none;fillColor=none;align=center;fontSize=8;fontColor=#444444;" vertex="1" parent="1"><mxGeometry x="525" y="1556" width="22" height="12" as="geometry" /></mxCell>
        <mxCell id="tb_m6" value="Jun" style="text;html=1;strokeColor=none;fillColor=none;align=center;fontSize=8;fontColor=#444444;" vertex="1" parent="1"><mxGeometry x="575" y="1556" width="22" height="12" as="geometry" /></mxCell>
        <mxCell id="tb_m7" value="Jul" style="text;html=1;strokeColor=none;fillColor=none;align=center;fontSize=8;fontColor=#444444;" vertex="1" parent="1"><mxGeometry x="625" y="1556" width="22" height="12" as="geometry" /></mxCell>
        <mxCell id="tb_m8" value="Agu" style="text;html=1;strokeColor=none;fillColor=none;align=center;fontSize=8;fontColor=#444444;" vertex="1" parent="1"><mxGeometry x="675" y="1556" width="22" height="12" as="geometry" /></mxCell>
        <mxCell id="tb_m9" value="Sep" style="text;html=1;strokeColor=none;fillColor=none;align=center;fontSize=8;fontColor=#444444;" vertex="1" parent="1"><mxGeometry x="725" y="1556" width="22" height="12" as="geometry" /></mxCell>
        <mxCell id="tb_m10" value="Okt" style="text;html=1;strokeColor=none;fillColor=none;align=center;fontSize=8;fontColor=#444444;" vertex="1" parent="1"><mxGeometry x="770" y="1556" width="22" height="12" as="geometry" /></mxCell>
        <mxCell id="tb_m11" value="Nov" style="text;html=1;strokeColor=none;fillColor=none;align=center;fontSize=8;fontColor=#444444;" vertex="1" parent="1"><mxGeometry x="815" y="1556" width="22" height="12" as="geometry" /></mxCell>
        <mxCell id="tb_m12" value="Des" style="text;html=1;strokeColor=none;fillColor=none;align=center;fontSize=8;fontColor=#444444;" vertex="1" parent="1"><mxGeometry x="860" y="1556" width="22" height="12" as="geometry" /></mxCell>

        <!-- KANAN: DISTRIBUSI STATUS (DONUT/PIE CHART) (w=315, x=950) -->
        <mxCell id="box_status_dist" value="" style="rounded=6;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#cccccc;strokeWidth=1;" vertex="1" parent="1">
          <mxGeometry x="950" y="1380" width="315" height="210" as="geometry" />
        </mxCell>
        <mxCell id="lbl_dist_title" value="DISTRIBUSI STATUS" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontStyle=1;fontColor=#444444;" vertex="1" parent="1">
          <mxGeometry x="965" y="1388" width="200" height="18" as="geometry" />
        </mxCell>

        <mxCell id="pie_outer" value="" style="ellipse;whiteSpace=wrap;html=1;aspect=fixed;fillColor=#222222;strokeColor=#000000;strokeWidth=1.5;" vertex="1" parent="1">
          <mxGeometry x="1065" y="1413" width="85" height="85" as="geometry" />
        </mxCell>
        <mxCell id="pie_inner" value="98.9%&#xa;Close" style="ellipse;whiteSpace=wrap;html=1;aspect=fixed;fillColor=#ffffff;strokeColor=#000000;strokeWidth=1;fontSize=10;fontStyle=1;align=center;" vertex="1" parent="1">
          <mxGeometry x="1085" y="1433" width="45" height="45" as="geometry" />
        </mxCell>

        <!-- Legend Status -->
        <mxCell id="leg_st1" value="■ Open: 1 (0.5%)" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=9;fontColor=#333333;" vertex="1" parent="1"><mxGeometry x="970" y="1513" width="130" height="16" as="geometry" /></mxCell>
        <mxCell id="leg_st2" value="■ Diproses: 1 (0.5%)" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=9;fontColor=#333333;" vertex="1" parent="1"><mxGeometry x="970" y="1533" width="130" height="16" as="geometry" /></mxCell>
        <mxCell id="leg_st3" value="■ Close: 180 (98.9%)" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=9;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="970" y="1553" width="130" height="16" as="geometry" /></mxCell>

        <!-- BAWAH: TOP CUSTOMER MINI-TABLE (w=990, x=275, y=1600) -->
        <mxCell id="box_top_cust" value="" style="rounded=6;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#cccccc;strokeWidth=1;" vertex="1" parent="1">
          <mxGeometry x="275" y="1600" width="990" height="172" as="geometry" />
        </mxCell>
        <mxCell id="lbl_tc_title" value="TOP CUSTOMER 2026 (KOMPLAIN TERBANYAK)" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=10;fontStyle=1;fontColor=#444444;" vertex="1" parent="1">
          <mxGeometry x="290" y="1606" width="350" height="18" as="geometry" />
        </mxCell>

        <!-- Cust 1 -->
        <mxCell id="tc_1_no" value="1." style="text;html=1;strokeColor=none;fillColor=none;align=right;fontSize=9;fontColor=#666666;" vertex="1" parent="1"><mxGeometry x="290" y="1628" width="18" height="16" as="geometry" /></mxCell>
        <mxCell id="tc_1_name" value="PT Astra Honda Motor" style="text;html=1;strokeColor=none;fillColor=none;align=left;fontSize=9;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="313" y="1628" width="200" height="16" as="geometry" /></mxCell>
        <mxCell id="tc_1_bar_bg" value="" style="rounded=2;whiteSpace=wrap;html=1;fillColor=#eeeeee;strokeColor=none;" vertex="1" parent="1"><mxGeometry x="520" y="1632" width="600" height="8" as="geometry" /></mxCell>
        <mxCell id="tc_1_bar" value="" style="rounded=2;whiteSpace=wrap;html=1;fillColor=#000000;strokeColor=none;" vertex="1" parent="1"><mxGeometry x="520" y="1632" width="425" height="8" as="geometry" /></mxCell>
        <mxCell id="tc_1_val" value="42 kasus (23%)" style="text;html=1;strokeColor=none;fillColor=none;align=right;fontSize=9;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="1130" y="1628" width="125" height="16" as="geometry" /></mxCell>

        <!-- Cust 2 -->
        <mxCell id="tc_2_no" value="2." style="text;html=1;strokeColor=none;fillColor=none;align=right;fontSize=9;fontColor=#666666;" vertex="1" parent="1"><mxGeometry x="290" y="1652" width="18" height="16" as="geometry" /></mxCell>
        <mxCell id="tc_2_name" value="PT Yamaha Indonesia Motor" style="text;html=1;strokeColor=none;fillColor=none;align=left;fontSize=9;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="313" y="1652" width="200" height="16" as="geometry" /></mxCell>
        <mxCell id="tc_2_bar_bg" value="" style="rounded=2;whiteSpace=wrap;html=1;fillColor=#eeeeee;strokeColor=none;" vertex="1" parent="1"><mxGeometry x="520" y="1656" width="600" height="8" as="geometry" /></mxCell>
        <mxCell id="tc_2_bar" value="" style="rounded=2;whiteSpace=wrap;html=1;fillColor=#555555;strokeColor=none;" vertex="1" parent="1"><mxGeometry x="520" y="1656" width="350" height="8" as="geometry" /></mxCell>
        <mxCell id="tc_2_val" value="35 kasus (19%)" style="text;html=1;strokeColor=none;fillColor=none;align=right;fontSize=9;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="1130" y="1652" width="125" height="16" as="geometry" /></mxCell>

        <!-- Cust 3 -->
        <mxCell id="tc_3_no" value="3." style="text;html=1;strokeColor=none;fillColor=none;align=right;fontSize=9;fontColor=#666666;" vertex="1" parent="1"><mxGeometry x="290" y="1676" width="18" height="16" as="geometry" /></mxCell>
        <mxCell id="tc_3_name" value="PT Denso Indonesia" style="text;html=1;strokeColor=none;fillColor=none;align=left;fontSize=9;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="313" y="1676" width="200" height="16" as="geometry" /></mxCell>
        <mxCell id="tc_3_bar_bg" value="" style="rounded=2;whiteSpace=wrap;html=1;fillColor=#eeeeee;strokeColor=none;" vertex="1" parent="1"><mxGeometry x="520" y="1680" width="600" height="8" as="geometry" /></mxCell>
        <mxCell id="tc_3_bar" value="" style="rounded=2;whiteSpace=wrap;html=1;fillColor=#888888;strokeColor=none;" vertex="1" parent="1"><mxGeometry x="520" y="1680" width="280" height="8" as="geometry" /></mxCell>
        <mxCell id="tc_3_val" value="28 kasus (15%)" style="text;html=1;strokeColor=none;fillColor=none;align=right;fontSize=9;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="1130" y="1676" width="125" height="16" as="geometry" /></mxCell>

        <!-- Cust 4 -->
        <mxCell id="tc_4_no" value="4." style="text;html=1;strokeColor=none;fillColor=none;align=right;fontSize=9;fontColor=#666666;" vertex="1" parent="1"><mxGeometry x="290" y="1700" width="18" height="16" as="geometry" /></mxCell>
        <mxCell id="tc_4_name" value="PT Showa Indonesia Mfg" style="text;html=1;strokeColor=none;fillColor=none;align=left;fontSize=9;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="313" y="1700" width="200" height="16" as="geometry" /></mxCell>
        <mxCell id="tc_4_bar_bg" value="" style="rounded=2;whiteSpace=wrap;html=1;fillColor=#eeeeee;strokeColor=none;" vertex="1" parent="1"><mxGeometry x="520" y="1704" width="600" height="8" as="geometry" /></mxCell>
        <mxCell id="tc_4_bar" value="" style="rounded=2;whiteSpace=wrap;html=1;fillColor=#aaaaaa;strokeColor=none;" vertex="1" parent="1"><mxGeometry x="520" y="1704" width="185" height="8" as="geometry" /></mxCell>
        <mxCell id="tc_4_val" value="18 kasus (10%)" style="text;html=1;strokeColor=none;fillColor=none;align=right;fontSize=9;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="1130" y="1700" width="125" height="16" as="geometry" /></mxCell>

        <!-- Cust 5 -->
        <mxCell id="tc_5_no" value="5." style="text;html=1;strokeColor=none;fillColor=none;align=right;fontSize=9;fontColor=#666666;" vertex="1" parent="1"><mxGeometry x="290" y="1724" width="18" height="16" as="geometry" /></mxCell>
        <mxCell id="tc_5_name" value="PT Kayaba Indonesia" style="text;html=1;strokeColor=none;fillColor=none;align=left;fontSize=9;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="313" y="1724" width="200" height="16" as="geometry" /></mxCell>
        <mxCell id="tc_5_bar_bg" value="" style="rounded=2;whiteSpace=wrap;html=1;fillColor=#eeeeee;strokeColor=none;" vertex="1" parent="1"><mxGeometry x="520" y="1728" width="600" height="8" as="geometry" /></mxCell>
        <mxCell id="tc_5_bar" value="" style="rounded=2;whiteSpace=wrap;html=1;fillColor=#cccccc;strokeColor=none;" vertex="1" parent="1"><mxGeometry x="520" y="1728" width="145" height="8" as="geometry" /></mxCell>
        <mxCell id="tc_5_val" value="14 kasus (8%)" style="text;html=1;strokeColor=none;fillColor=none;align=right;fontSize=9;fontStyle=1;fontColor=#000000;" vertex="1" parent="1"><mxGeometry x="1130" y="1724" width="125" height="16" as="geometry" /></mxCell>

      </root>
    </mxGraphModel>
  </diagram>
</mxfile>
'''

with open('Wireframe_Dashboard_Supervisor.drawio.xml', 'w', encoding='utf-8') as f:
    f.write(xml_content.strip() + '\n')

print("Updated Wireframe_Dashboard_Supervisor.drawio.xml successfully with separated Pareto and Apriori sections!")
