import pandas as pd
from itertools import combinations

df = pd.read_excel(r'c:\laragon\www\Apriori\salsa-arpriory\_bahan-skripsi\cleardata.xlsx', header=1)

transactions_dict = {}
for idx, row in df.iterrows():
    cid = row['No Customer']
    ket = str(row['Apriori Ketidaksesuain']).strip().lower()
    pen = str(row['Apriori Penyebab']).strip().lower()
    
    if cid not in transactions_dict:
        transactions_dict[cid] = []
        
    if ket not in ['', '-', 'nan', 'null']:
        transactions_dict[cid].append('Ketidaksesuaian=' + ket)
    if pen not in ['', '-', 'nan', 'null']:
        transactions_dict[cid].append('Penyebab=' + pen)

transactions = [list(set(b)) for b in transactions_dict.values() if b]

n = len(transactions)
min_support = 0.015
min_confidence = 0.5
min_count = min_support * n

C1 = {}
for t in transactions:
    for item in t:
        C1[item] = C1.get(item, 0) + 1

L1 = {k: v for k, v in C1.items() if v >= min_count}

C2 = {}
for t in transactions:
    for item1, item2 in combinations(sorted(t), 2):
        if item1 in L1 and item2 in L1:
            pair = (item1, item2)
            C2[pair] = C2.get(pair, 0) + 1

L2 = {k: v for k, v in C2.items() if v >= min_count}

rules = []
for pair, count in L2.items():
    support = count / n
    item1, item2 = pair
    
    conf1 = support / (L1[item1] / n)
    if conf1 >= min_confidence:
        lift1 = conf1 / (L1[item2] / n)
        rules.append({'ant': item1, 'con': item2, 'support': support, 'conf': conf1, 'lift': lift1})
        
    conf2 = support / (L1[item2] / n)
    if conf2 >= min_confidence:
        lift2 = conf2 / (L1[item1] / n)
        rules.append({'ant': item2, 'con': item1, 'support': support, 'conf': conf2, 'lift': lift2})

rules.sort(key=lambda x: x['lift'], reverse=True)

md = []
md.append('# Perhitungan Manual Algoritma Apriori\n')
md.append('Proses pencarian pola asosiasi (Apriori) pada dataset cacat produk.')
md.append(f'- **Total Transaksi (N):** {n}')
md.append(f'- **Minimum Support:** {min_support * 100}%')
md.append(f'- **Minimum Confidence:** {min_confidence * 100}%\n')

md.append('## Pembentukan Itemset 1 (C1 & L1)')
md.append('Frekuensi kemunculan setiap item tunggal (Ketidaksesuaian dan Penyebab) dalam setiap transaksi pelanggan (No Customer).\n')
md.append('| Item | Jumlah (Count) | Support | Keterangan |')
md.append('|---|---|---|---|')
for item, count in sorted(C1.items(), key=lambda x: x[1], reverse=True)[:15]:
    sup = count/n
    status = 'Memenuhi' if count >= min_count else 'Tidak Memenuhi'
    clean_item = item.replace('ketidaksesuaian=', 'Ketidaksesuaian: ').replace('penyebab=', 'Penyebab: ').title()
    md.append(f'| {clean_item} | {count} | {sup*100:.1f}% | {status} |')
md.append('*(Catatan: Tabel dipotong untuk 15 item dengan frekuensi tertinggi)*\n')

md.append('## Pembentukan Itemset 2 (C2 & L2)')
md.append('Kombinasi 2 item yang memenuhi nilai Minimum Support pada satu transaksi yang sama.\n')
md.append('| Itemset | Jumlah (Count) | Support | Keterangan |')
md.append('|---|---|---|---|')
for pair, count in sorted(C2.items(), key=lambda x: x[1], reverse=True)[:15]:
    sup = count/n
    status = 'Memenuhi' if count >= min_count else 'Tidak Memenuhi'
    clean_pair = f"{pair[0].replace('ketidaksesuaian=','').replace('penyebab=','').title()}, {pair[1].replace('ketidaksesuaian=','').replace('penyebab=','').title()}"
    md.append(f'| {clean_pair} | {count} | {sup*100:.1f}% | {status} |')

md.append('\n## Pembentukan Aturan Asosiasi (Association Rules)')
md.append('Mencari aturan asosiasi yang memenuhi nilai Minimum Confidence (>= 50%).\n')
md.append('| Aturan (Rule) | Support | Confidence | Lift Ratio | Kekuatan |')
md.append('|---|---|---|---|---|')
for r in rules[:20]:
    clean_ant = r['ant'].replace('ketidaksesuaian=','').replace('penyebab=','').title()
    clean_con = r['con'].replace('ketidaksesuaian=','').replace('penyebab=','').title()
    
    if r['lift'] >= 5: kek = 'Sangat Kuat'
    elif r['lift'] >= 3: kek = 'Kuat'
    elif r['lift'] >= 1.5: kek = 'Sedang'
    else: kek = 'Lemah'
    
    md.append(f"| Jika {clean_ant} maka {clean_con} | {r['support']*100:.1f}% | {r['conf']*100:.1f}% | {r['lift']:.2f} | {kek} |")

md.append('\n## Kesimpulan (Interpretasi)')
for i, r in enumerate(rules[:3], 1):
    clean_ant = r['ant'].replace('ketidaksesuaian=','').replace('penyebab=','').title()
    clean_con = r['con'].replace('ketidaksesuaian=','').replace('penyebab=','').title()
    md.append(f"{i}. Dari semua kasus **{clean_ant}**, sebanyak **{r['conf']*100:.0f}%** terkait dengan **{clean_con}**. Nilai kaitan keduanya memiliki Lift Ratio **{r['lift']:.1f}** yang menandakan bahwa hubungannya sangat valid.")

out_path = r'c:\laragon\www\Apriori\salsa-arpriory\perhitungan_apriori_bab3.md'
with open(out_path, 'w', encoding='utf-8') as f:
    f.write('\n'.join(md))
print(f"Generated {out_path} with N={n}")
