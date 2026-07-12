import pandas as pd
from mlxtend.preprocessing import TransactionEncoder
from mlxtend.frequent_patterns import apriori, association_rules

# ---- 1. Baca data (header ada di baris ke-2, baris pertama kosong) ----
FILE = "DataWBERPACK24.xlsx"
df = pd.read_excel(FILE, skiprows=1)
df.columns = df.columns.str.strip()

# ---- 2. Pilih kolom yang dijadikan item ----
KOLOM_ITEM = [
    "Apriori Ketidaksesuain",
    "Apriori Penyebab",
]

# Bersihkan: buang spasi berlebih
for c in KOLOM_ITEM:
    df[c] = df[c].astype(str).str.strip()

# ---- 3. Bentuk transaksi ----
transaksi = []
for _, row in df.iterrows():
    items = []
    for c in KOLOM_ITEM:
        val = row[c]
        if val and val.lower() not in ("nan", "-", ""):
            items.append(f"{c}={val}")
    if items:
        transaksi.append(items)

print(f"Jumlah transaksi: {len(transaksi)}")

# ---- 4. One-hot encode ----
te = TransactionEncoder()
te_ary = te.fit(transaksi).transform(transaksi)
df_onehot = pd.DataFrame(te_ary, columns=te.columns_)

# ---- 5. Frequent itemsets ----
MIN_SUPPORT = 0.05      # 5% — turunkan kalau hasil sedikit
frequent = apriori(df_onehot, min_support=MIN_SUPPORT, use_colnames=True)
frequent = frequent.sort_values("support", ascending=False)

print("\n=== FREQUENT ITEMSETS ===")
print(frequent.to_string(index=False))

# ---- 6. Association rules ----
MIN_CONFIDENCE = 0.5    # 50%
rules = association_rules(frequent, metric="confidence", min_threshold=MIN_CONFIDENCE)

# rapikan tampilan antecedents/consequents + buang prefix nama kolom
def bersih(itemset):
    teks = ", ".join(sorted(itemset))
    return teks.replace("Apriori Ketidaksesuain=", "").replace("Apriori Penyebab=", "")

rules["antecedents"] = rules["antecedents"].apply(bersih)
rules["consequents"] = rules["consequents"].apply(bersih)

# urutkan dari lift tertinggi (kaitan paling kuat di atas)
rules = rules.sort_values("lift", ascending=False).reset_index(drop=True)

# ---- 6b. Tambah kolom KEKUATAN dan INTERPRETASI ----
def kekuatan(lift):
    if lift >= 5:   return "Sangat Kuat"
    if lift >= 3:   return "Kuat"
    if lift >= 1.5: return "Sedang"
    return "Lemah"

def interpretasi(r):
    return (f'Dari semua kasus "{r["antecedents"]}", sebanyak '
            f'{r["confidence"]*100:.0f}% terkait dengan "{r["consequents"]}". '
            f'Kaitan keduanya {kekuatan(r["lift"]).lower()} (lift {r["lift"]:.1f}x).')

rules["Kekuatan Kaitan"] = rules["lift"].apply(kekuatan)
rules["Interpretasi"] = rules.apply(interpretasi, axis=1)

kolom_tampil = ["antecedents", "consequents", "support", "confidence",
                "lift", "Kekuatan Kaitan", "Interpretasi"]

print("\n=== ASSOCIATION RULES ===")
print(rules[kolom_tampil].to_string(index=False))

# ---- 7. Simpan ke Excel ----
with pd.ExcelWriter("hasil_apriori.xlsx") as writer:
    frequent_out = frequent.copy()
    frequent_out["itemsets"] = frequent_out["itemsets"].apply(lambda x: ", ".join(sorted(x)))
    frequent_out.to_excel(writer, sheet_name="Frequent Itemsets", index=False)
    rules[kolom_tampil].to_excel(writer, sheet_name="Association Rules", index=False)

print("\nHasil disimpan ke hasil_apriori.xlsx")