<?php
/**
 * Simple PO to MO Compiler
 *
 * This script converts .po files to .mo files for WordPress translations
 */

function po_to_mo($po_file, $mo_file) {
    $po_content = file_get_contents($po_file);

    // Parse PO file
    $entries = array();
    $lines = explode("\n", $po_content);
    $current_msgid = '';
    $current_msgstr = '';
    $in_msgid = false;
    $in_msgstr = false;

    foreach ($lines as $line) {
        $line = trim($line);

        if (strpos($line, 'msgid "') === 0) {
            // Save previous entry
            if ($current_msgid && $current_msgstr) {
                $entries[$current_msgid] = $current_msgstr;
            }

            $current_msgid = substr($line, 7, -1);
            $current_msgstr = '';
            $in_msgid = true;
            $in_msgstr = false;
        } elseif (strpos($line, 'msgstr "') === 0) {
            $current_msgstr = substr($line, 8, -1);
            $in_msgid = false;
            $in_msgstr = true;
        } elseif (strpos($line, '"') === 0 && strlen($line) > 2) {
            $text = substr($line, 1, -1);
            if ($in_msgid) {
                $current_msgid .= $text;
            } elseif ($in_msgstr) {
                $current_msgstr .= $text;
            }
        }
    }

    // Save last entry
    if ($current_msgid && $current_msgstr) {
        $entries[$current_msgid] = $current_msgstr;
    }

    // Create MO file
    $mo = '';

    // MO file header
    $mo .= pack('L', 0x950412de); // Magic number
    $mo .= pack('L', 0);           // Version
    $mo .= pack('L', count($entries)); // Number of entries
    $mo .= pack('L', 28);          // Offset of table with original strings
    $mo .= pack('L', 28 + count($entries) * 8); // Offset of table with translation strings
    $mo .= pack('L', 0);           // Size of hashing table
    $mo .= pack('L', 0);           // Offset of hashing table

    // Calculate offsets and lengths
    $originals_table = '';
    $translations_table = '';
    $originals = '';
    $translations = '';
    $offset = 28 + count($entries) * 16;

    foreach ($entries as $original => $translation) {
        if (!$original) continue; // Skip header

        $originals_table .= pack('L', strlen($original));
        $originals_table .= pack('L', $offset);
        $originals .= $original . "\0";
        $offset += strlen($original) + 1;
    }

    foreach ($entries as $original => $translation) {
        if (!$original) continue; // Skip header

        $translations_table .= pack('L', strlen($translation));
        $translations_table .= pack('L', $offset);
        $translations .= $translation . "\0";
        $offset += strlen($translation) + 1;
    }

    $mo .= $originals_table;
    $mo .= $translations_table;
    $mo .= $originals;
    $mo .= $translations;

    file_put_contents($mo_file, $mo);

    return true;
}

// Compile Vietnamese translation
$po_file = __DIR__ . '/vi_VN.po';
$mo_file = __DIR__ . '/vi_VN.mo';

if (file_exists($po_file)) {
    if (po_to_mo($po_file, $mo_file)) {
        echo "✓ Successfully compiled vi_VN.po to vi_VN.mo\n";
    } else {
        echo "✗ Failed to compile translation\n";
    }
} else {
    echo "✗ File vi_VN.po not found\n";
}
