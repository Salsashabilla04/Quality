import json, re

path = r'C:\Users\salsa\.gemini\antigravity-ide\brain\fbf6bcd7-8b08-4200-a9ab-7be201e29630\.system_generated\logs\transcript_full.jsonl'
with open(path, 'r', encoding='utf-8') as f:
    lines_json = [json.loads(line) for line in f]

# 1-608
t2 = lines_json[7]['content']
m1 = {int(m[0]): m[1] for m in re.findall(r'^(\d+): (.*)$', t2, re.MULTILINE)}

# 660-705
t5 = lines_json[27]['content']
m2 = {int(m[0]): m[1] for m in re.findall(r'^(\d+): (.*)$', t5, re.MULTILINE)}

# 700-787
t4 = lines_json[25]['content']
m3 = {int(m[0]): m[1] for m in re.findall(r'^(\d+): (.*)$', t4, re.MULTILINE)}

lines = {}
for k, v in m1.items():
    lines[k] = v

lines[608] = '                        <span>Jadwalkan Kunjungan Lapangan (Visit Customer)</span>'
lines[609] = '                    </span>'
lines[610] = '                </label>'
lines[611] = ''
lines[612] = '                <div id="visitFieldsBox" class="hidden space-y-3 pl-6 border-l-2 border-violet-300 mt-2">'
lines[613] = '                    <div class="grid grid-cols-2 gap-2">'
lines[614] = '                        <div>'
lines[615] = '                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Tanggal Rencana Kunjungan</label>'
lines[616] = '                            <input type="date" name="tanggal_visit" id="approveTanggalVisit" min="{{ date(\'Y-m-d\') }}" class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium">'
lines[617] = '                        </div>'
lines[618] = '                        <div>'
lines[619] = '                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Jam Visit</label>'
lines[620] = '                            <input type="time" name="jam_visit" id="approveJamVisit" class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium">'
lines[621] = '                        </div>'
lines[622] = '                    </div>'
lines[623] = '                    <div>'
lines[624] = '                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Catatan Kunjungan Lapangan</label>'
lines[625] = '                        <input type="text" name="catatan_visit" id="approveCatatanVisit" placeholder="Misal: Cek fisik sampel Moisture Content di lokasi customer"'
lines[626] = '                               class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium">'
lines[627] = '                    </div>'
lines[628] = '                </div>'
lines[629] = '            </div>'
lines[630] = ''
lines[631] = '            <div class="flex gap-3 pt-2">'
lines[632] = '                <button type="submit" class="flex-1 btn btn-primary text-sm py-2.5 rounded-lg shadow-sm">'
lines[633] = '                    Simpan Validasi CAPA &amp; NCR'
lines[634] = '                </button>'
lines[635] = '                <button type="button" onclick="document.getElementById(\'approveModal\').classList.add(\'hidden\')" class="flex-1 btn btn-light border border-slate-200 text-sm py-2.5 rounded-lg">'
lines[636] = '                    Batal'
lines[637] = '                </button>'
lines[638] = '            </div>'
lines[639] = '        </form>'
lines[640] = '    </div>'
lines[641] = '</div>'
lines[642] = '@endcan'
lines[643] = ''
lines[644] = '{{-- Import Modal --}}'
lines[645] = '@can(\'manage-complaints\')'
lines[646] = '<div id="importModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">'
lines[647] = '    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">'
lines[648] = '        <div class="px-6 py-4 border-b border-slate-100 bg-[#f6c23e] text-slate-800 flex items-center justify-between font-bold">'
lines[649] = '            <h3 class="text-base font-bold flex items-center gap-2">'
lines[650] = '                <i class="fas fa-file-import"></i>'
lines[651] = '                <span>Import Data Complaint Excel</span>'
lines[652] = '            </h3>'
lines[653] = '            <button onclick="document.getElementById(\'importModal\').classList.add(\'hidden\')" class="text-slate-600 hover:text-slate-900 font-bold text-lg">&times;</button>'
lines[654] = '        </div>'
lines[655] = '        <form action="{{ route(\'complaints.import\') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">'
lines[656] = '            @csrf'
lines[657] = '            <div>'
lines[658] = '                <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih File Excel (.xlsx / .xls)</label>'
lines[659] = '                <input type="file" name="file" required accept=".xlsx,.xls" class="w-full text-sm border border-slate-300 rounded-lg p-2">'

for k, v in m2.items():
    lines[k] = v
for k, v in m3.items():
    lines[k] = v

total_lines = max(lines.keys())
print(f'Total restored lines: {total_lines}')

out_text = '\n'.join([lines.get(i, '') for i in range(1, total_lines + 1)]) + '\n'
with open(r'c:\laragon\www\Apriori\salsa-arpriory\resources\views\complaints\index.blade.php', 'w', encoding='utf-8') as f:
    f.write(out_text)
print('Successfully restored resources/views/complaints/index.blade.php!')
