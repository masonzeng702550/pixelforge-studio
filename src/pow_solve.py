#!/usr/bin/env python3
"""PixelForge Human Shield proof-of-work solver.

Usage:  python3 pow_solve.py 'PF1;<bits>;<prefix>'

Finds a nonce such that sha256(prefix + nonce) has <bits> leading zero bits and
prints it. Paste that nonce into the Human Shield form (or POST it as `solution`).
"""
import sys
import hashlib


def leading_zero_bits(b: bytes) -> int:
    n = 0
    for byte in b:
        if byte == 0:
            n += 8
            continue
        for i in range(7, -1, -1):
            if (byte >> i) & 1:
                return n
            n += 1
        return n
    return n


def main() -> int:
    if len(sys.argv) != 2 or sys.argv[1].count(";") != 2:
        sys.stderr.write("usage: pow_solve.py 'PF1;<bits>;<prefix>'\n")
        return 2
    ver, bits_s, prefix = sys.argv[1].split(";")
    if ver != "PF1":
        sys.stderr.write("unknown challenge version\n")
        return 2
    bits = int(bits_s)
    i = 0
    while True:
        nonce = str(i)
        if leading_zero_bits(hashlib.sha256((prefix + nonce).encode()).digest()) >= bits:
            print(nonce)
            return 0
        i += 1


if __name__ == "__main__":
    raise SystemExit(main())
