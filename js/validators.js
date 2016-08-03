
 /*
 * phpMumbleAdmin (PMA), web php administration tool for murmur (mumble server daemon).
 * Copyright (C) 2010 - 2015  Dadon David. PMA@ipnoz.net
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/*
* Don't submit unchanged value, and dont allow empty value if not allowed
*
* Return false on error
*/
function unchanged(doc, empty_not_allowed)
{
    // Select field
    if (doc.tagName === 'SELECT') {
        if (doc.options[0].selected === true) {
            doc.focus();
            return false;
        }
    // Input, textarea
    } else if (doc.value === doc.defaultValue) {
        doc.select();
        return false;
    // Deny empty value if modified but not allowed
    } else if (typeof(empty_not_allowed) !== 'undefined' && empty_not_allowed === 'true') {

        if (doc.value === '') {
            doc.select();
            return false;
        }
    }
}

/**
* Check if a form has been modified.
* @Return boolean.
*/
function isFormModified(form)
{
    var list = form.elements;

    for (i = 0; i < list.length; ++i) {

        switch (list[i].type) {
            case 'text':
            case 'password':
            case 'textarea':
            case 'range':
            case 'email':
            case 'number':
                if (list[i].value !== list[i].defaultValue) {
                    return true;
                }
                break;

            case 'checkbox':
            case 'radio':
                if (list[i].checked !== list[i].defaultChecked) {
                    return true;
                }
                break;

            case 'select-one':
            case 'select-multiple':
                var hasDefault = false;
                var opt = list[i].options;
                for (y = 0; y < opt.length; ++y) {
                    if (opt[y].defaultSelected) {
                        hasDefault = true;
                        if (! opt[y].selected) {
                            return true;
                        }
                    }
                }
                /**
                * No defaultSelect found
                */
                if (! hasDefault) {
                    if (opt[0].selected === false) {
                        return true;
                    }
                }
                break;
        }
    }

    highLightElement(form);
    return false;
}

/**
* Validate a password form:
*
* Return false on error
*/
function validatePw(form)
{
    // If current password is required:
    if (typeof(form.current) !== 'undefined') {
        if (form.current.value === '') {
            form.current.select();
            return false;
        }
    }
    // Empty new pw field:
    if (form.new_pw.value === '') {
        form.new_pw.select();
        return false;
    }
    // Empty confirm pw field:
    if (form.confirm_new_pw.value == '') {
        form.confirm_new_pw.select();
        return false;
    }
    // Validate new_pw & confirm_new_pw:
    if (form.new_pw.value !== form.confirm_new_pw.value) {
        form.new_pw.value = '';
        form.confirm_new_pw.value = '';
        form.new_pw.select();
        alert(TEXT.pw_check_failed);
        return false;
    }
}

/*
* Page administration
*/
function validateAdminEditor(form)
{
    if (! isFormModified(form)) {
        return false;
    }
    if (form.new_pw.value !== '') {
        return validatePw(form);
    }
}

/*
* Page configuration
*/
function validateSuperAdmin(form)
{
    if (
        form.login.value === form.login.defaultValue &&
        form.new_pw.value === form.new_pw.defaultValue &&
        form.confirm_new_pw.value === form.confirm_new_pw.defaultValue
    ) {
        highLightElement(form);
        return false;
    }
    if (form.new_pw.value !== '') {
        return validatePw(form);
    }
}

/*
* Page vserver
*/
function validateSrvSettings(form)
{
    if (! isFormModified(form)) {
        return false;
    }
    // PORT
    if (typeof(form.port) !== 'undefined' && form.port.value !== '') {
        if (! check_port(form.port.value)) {
            form.port.select();
            alert(TEXT.invalid_port);
            return false;
        }
    }
    // TIMEOUT
    if (typeof(form.timeout) !== 'undefined' && form.timeout.value !== '') {
        if (! check_digital(form.timeout.value) || form.timeout.value < 0) {
            form.timeout.select();
            alert(TEXT.invalid_number.replace('%s', 'timeout'));
            return false;
        }
    }
    // BANDWIDTH
    if (typeof(form.bandwidth) !== 'undefined' && form.bandwidth.value !== '') {
        if (! check_digital(form.bandwidth.value)) {
            form.bandwidth.select();
            alert(TEXT.invalid_number.replace('%s', 'bandwitch'));
            return false;
        }
    }
    // USERS
    if (typeof(form.users) !== 'undefined' && form.users.value !== '') {
        if (! check_digital(form.users.value)) {
            form.users.select();
            alert(TEXT.invalid_number.replace('%s', 'users'));
            return false;
        }
    }
    // DEFAULT CHANNEL
    if (typeof(form.defaultchannel) !== 'undefined' && form.defaultchannel.value !== '') {
        if (! check_digital(form.defaultchannel.value)) {
            form.defaultchannel.select();
            alert(TEXT.invalid_number.replace('%s', 'defaultchannel'));
            return false;
        }
    }
    // USERS PER CHANNEL
    if (typeof(form.usersperchannel) !== 'undefined' && form.usersperchannel.value !== '') {
        if (! check_digital(form.usersperchannel.value)) {
            form.usersperchannel.select();
            alert(TEXT.invalid_number.replace('%s', 'userperchannel'));
            return false;
        }
    }
    // TEXT MSG LENGTH
    if (typeof(form.textmessagelength) !== 'undefined' && form.textmessagelength.value !== '') {
        if (! check_digital(form.textmessagelength.value)) {
            form.textmessagelength.select();
            alert(TEXT.invalid_number.replace('%s', 'textmessagelength'));
            return false;
        }
    }
    // IMAGE MSG LENGTH
    if (typeof(form.imagemessagelength) !== 'undefined' && form.imagemessagelength.value !== '') {
        if (! check_digital(form.imagemessagelength.value)) {
            form.imagemessagelength.select();
            alert(TEXT.invalid_number.replace('%s', 'imagemessagelength'));
            return false;
        }
    }
}

function validateMumbleRegistrationEditor(form)
{
    if (! isFormModified(form)) {
        return false;
    }
    if (typeof(form.new_pw.current) !== 'undefined') {
        if (form.new_pw.current !== '') {
            return validatePw(form);
        }
    }
    if (form.new_pw.value !== '') {
        return validatePw(form);
    }
}

function validateBanEditor(doc)
{
    // Check for a valid IP & mask:
    ip_is_valid = false;
    mask_is_valid = false;
    // ipv4
    if (check_ipv4(doc.ip.value)) {
        ip_is_valid = true;
        // Range 1-32
        if (doc.mask.value.search(/^[1-9]$|^[1-2][0-9]$|^3[0-2]$/) != -1) {
            mask_is_valid = true;
        }
    }
    // ipv6
    if (check_ipv6(doc.ip.value)) {
        ip_is_valid = true;
        // Range 1-128
        if (doc.mask.value.search(/^[1-9]$|^[1-9][0-9]$|^1[0-1][0-9]$|^12[0-8]$/) != -1) {
            mask_is_valid = true;
        }
    }
    // Empty mask is valid too.
    if (doc.mask.value === '') {
        mask_is_valid = true;
    }

    if (ip_is_valid === true) {
        if (mask_is_valid === false) {
            doc.mask.select();
            alert(TEXT.invalid_mask);
            return false;
        }
    } else {
        doc.ip.select();
        alert(TEXT.invalid_ip);
        return false;
    }
    // IP & mask are valid, check if the form have been modified:
    return isFormModified(doc);
}
