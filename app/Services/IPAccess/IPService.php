<?php

namespace App\Services\IPAccess;


use App\Models\IP;

class IPService
{
    static function check_ip($ip,$location = 'iran')
    {
        $ispes = IP::all();
        foreach ($ispes as $isp) {
            if (!$isp->first_ip) {
                if (str_contains($isp->cidr, ':')) {//ip is v6
                    $prefix = $isp->cidr;
// Split in address and prefix length
                    list($addr_given_str, $prefixlen) = explode('/', $prefix);
// Parse the address into a binary string
                    $addr_given_bin = inet_pton($addr_given_str);
// Convert the binary string to a string with hexadecimal characters
                    $addr_given_hex = bin2hex($addr_given_bin);
// Overwriting first address string to make sure notation is optimal
                    $addr_given_str = inet_ntop($addr_given_bin);
// Calculate the number of 'flexible' bits
                    $flexbits = 128 - $prefixlen;
// Build the hexadecimal strings of the first and last addresses
                    $addr_hex_first = $addr_given_hex;
                    $addr_hex_last = $addr_given_hex;
// We start at the end of the string (which is always 32 characters long)
                    $pos = 31;
                    while ($flexbits > 0) {
                        // Get the characters at this position
                        $orig_first = substr($addr_hex_first, $pos, 1);
                        $orig_last = substr($addr_hex_last, $pos, 1);
                        // Convert them to an integer
                        $origval_first = hexdec($orig_first);
                        $origval_last = hexdec($orig_last);
                        // First address: calculate the subnet mask. min() prevents the comparison from being negative
                        $mask = 0xf << (min(4, $flexbits));
                        // AND the original against its mask
                        $new_val_first = $origval_first & $mask;
                        // Last address: OR it with (2^flexbits)-1, with flexbits limited to 4 at a time
                        $new_val_last = $origval_last | (pow(2, min(4, $flexbits)) - 1);
                        // Convert them back to hexadecimal characters
                        $new_first = dechex($new_val_first);
                        $new_last = dechex($new_val_last);
                        // And put those character back in their strings
                        $addr_hex_first = substr_replace($addr_hex_first, $new_first, $pos, 1);
                        $addr_hex_last = substr_replace($addr_hex_last, $new_last, $pos, 1);
                        // We processed one nibble, move to previous position
                        $flexbits -= 4;
                        $pos -= 1;
                    }
// Convert the hexadecimal strings to a binary string
                    $addr_bin_first = hex2bin($addr_hex_first);
                    $addr_bin_last = hex2bin($addr_hex_last);
// And create an IPv6 address from the binary string
                    $addr_str_first = inet_ntop($addr_bin_first);
                    $addr_str_last = inet_ntop($addr_bin_last);
                    $isp->first_ip = $addr_str_first;
                    $isp->last_ip = $addr_str_last;
                } else {//ip is v4
                    $cidr = explode('/', $isp->cidr);
                    $first_ip_long = ip2long($cidr[0]);
                    $last_ip_long = $first_ip_long + pow(2, 32 - (int)$cidr[1]) - 1;
                    $isp->first_ip = $cidr[0];
                    $isp->last_ip = long2ip($last_ip_long);
                }
                $isp->save();
            }

            if (str_contains($isp->first_ip, ':')) {//ip is v6
                if ($isp->first_ip <= $ip && $isp->last_ip >= $ip)
                    return true;
            } else {
                if (ip2long($isp->first_ip) <= ip2long($ip) && ip2long($isp->last_ip) >= ip2long($ip))
                    return true;
            }

        }
        return false;
    }
}
