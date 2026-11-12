const descriptions = [
  'SIDE FRAME', 'WEB PLATE (R)', 'BOTTOM PLATE', 'WEB PLATE', 'STIFFENER',
  'WEB PLATE', 'SPACER PLATE', 'BACKING PLATE', 'TOP PLATE', 'BOTTOM PLATE',
  'WEB PLATE', 'STIFFENER', 'WEB PLATE', 'BACKING PLATE', 'SPACER PLATE',
  'SAFETY FOR CONTAINER', 'STIFFENER 1', 'CYLINDER BRAKE BRACKET', 'STIFFENER 2',
  'BRAKE LEVER BRACKET', 'BRACKET', 'TRUNION NUT BRACKET', 'PLATE',
  'DISTRIBUTOR VALVE BRACKET', 'STIFFENER 4', 'BRACKET', 'BRACKET',
  'PIPE BRACKET (1 1/4")', 'BRACKET', 'CROSSBEAM 1', 'CROSSBEAM 2',
  'LONGITUDINAL BEAM', 'BOLSTER BEAM', 'GUSSET PLATE', 'FLANGE (L)', 'FLANGE (R)',
  'COVER PLATE', 'STOPPER BLOCK', 'PIN BRACKET', 'SPRING SEAT', 'HANGER BRACKET',
  'CENTER SILL PLATE', 'END SILL', 'SIDE SILL', 'CENTER PIVOT SEAT',
  'COUPLER POCKET', 'DRAFT GEAR CARRIER', 'BUFFER BEAM PLATE', 'STRIKER CASTING',
  'BODY BOLSTER WEB', 'BODY BOLSTER COVER', 'CROSSBEAM GUSSET', 'FLOOR SUPPORT',
  'SIDE WALL STANCHION', 'ROOF CARLINE', 'CORNER POST', 'DOOR POST',
  'WINDOW FRAME BRACKET', 'INTERIOR BRACKET', 'BATTERY BOX BRACKET',
  'AIR RESERVOIR BRACKET', 'MAIN RESERVOIR PIPE BRACKET', 'BRAKE PIPE CLAMP',
  'CABLE TRAY BRACKET', 'JUNCTION BOX BRACKET', 'EARTHING BRACKET'
];

const proyeks = ['S-22002.1', 'S-22002.2'];
const items = [];

for (let i = 1; i <= 215; i++) {
    let no = i;
    if (i === 1) no = 1;
    else if (i === 2) no = 10;
    else if (i >= 3 && i <= 16) no = 97 + i; // 100, 101, 102...
    else no = (i * 7) % 200 + 1;

    let matNum = (111000 + i).toString().padStart(6, '0');
    let suffix = i % 7 === 0 ? 'R' : (i % 11 === 0 ? 'L' : (i % 13 === 0 ? '001' : ''));
    let code = 'B21' + ((i % 5) + 1) + 'I' + matNum + suffix;

    // Exactly 1 KOMPONEN (SUDAH PR) and 214 PIECE PART
    let group = (i === 10) ? 'KOMPONEN' : 'PIECE PART';

    // Status PR: 210 SUDAH PR (209 PIECE PART + 1 KOMPONEN), 3 BELUM PR, 1 STOK, 1 STATUS LAIN / TIDAK DIKENAL
    let statusPR = 'SUDAH PR';
    if (i === 20 || i === 40 || i === 60) {
        statusPR = 'BELUM PR'; // 3 BELUM PR
    } else if (i === 80) {
        statusPR = 'STOK'; // 1 STOK
    } else if (i === 100) {
        statusPR = 'STATUS LAIN / TIDAK DIKENAL'; // 1 STATUS LAIN
    }

    let desc = descriptions[(i - 1) % descriptions.length];
    if (i === 1) desc = 'SIDE FRAME';
    if (i === 2) desc = 'WEB PLATE (R)';
    if (i === 3) desc = 'BOTTOM PLATE';
    if (i === 4) desc = 'WEB PLATE';
    if (i === 5) desc = 'STIFFENER';
    if (i === 6) desc = 'WEB PLATE';
    if (i === 7) desc = 'SPACER PLATE';
    if (i === 8) desc = 'BACKING PLATE';
    if (i === 9) desc = 'TOP PLATE';
    if (i === 10) desc = 'DISTRIBUTOR VALVE BRACKET';

    items.push({
        index: i - 1,
        no: no,
        kode_konversi: code,
        group: group,
        tindak_lanjut: 'PR BARU',
        deskripsi: desc,
        kebutuhan: 0,
        qty_pr: 0,
        kode_material: code,
        trainset: 'TANPA TRAINSET',
        status_pr: statusPR,
        proyek: proyeks[i % 2]
    });
}

export const bomDataset = items;
