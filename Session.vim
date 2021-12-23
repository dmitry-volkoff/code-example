let SessionLoad = 1
let s:so_save = &so | let s:siso_save = &siso | set so=0 siso=0
let v:this_session=expand("<sfile>:p")
silent only
cd /mnt/d62e706e-b0b4-4937-be1e-ba79bb30e8a6/vdb/www/gd/contact-assigned-all
if expand('%') == '' && !&modified && line('$') <= 1 && getline(1) == ''
  let s:wipebuf = bufnr('%')
endif
set shortmess=aoO
badd +3 /mnt/d62e706e-b0b4-4937-be1e-ba79bb30e8a6/vdb/www/gd/rest/test.php
badd +3 export.php
badd +3 test.php
badd +6 src/Utils/Security.php
badd +283 src/Utils/Bx24Rest.php
badd +327 src/crest.php
badd +18 src/Utils/CrestExt.php
badd +183 ~/www/gd/deal-csv/deal-csv.php
badd +290 del_deals_tasks_by_user.php
badd +85 src/Utils/UICli.php
badd +122 src/Utils/UIHtml.php
badd +11 src/Utils/UIInterface.php
badd +19 ~/www/gd/contact-assigned-all/config.php
badd +1 bla.php
argglobal
%argdel
$argadd export.php
edit bla.php
set splitbelow splitright
set nosplitbelow
set nosplitright
wincmd t
set winminheight=0
set winheight=1
set winminwidth=0
set winwidth=1
argglobal
setlocal fdm=manual
setlocal fde=0
setlocal fmr={{{,}}}
setlocal fdi=#
setlocal fdl=0
setlocal fml=1
setlocal fdn=20
setlocal fen
silent! normal! zE
let s:l = 7 - ((6 * winheight(0) + 19) / 38)
if s:l < 1 | let s:l = 1 | endif
exe s:l
normal! zt
7
normal! 0
tabnext 1
if exists('s:wipebuf') && getbufvar(s:wipebuf, '&buftype') isnot# 'terminal'
  silent exe 'bwipe ' . s:wipebuf
endif
unlet! s:wipebuf
set winheight=1 winwidth=20 winminheight=1 winminwidth=1 shortmess=filnxtToOS
let s:sx = expand("<sfile>:p:r")."x.vim"
if file_readable(s:sx)
  exe "source " . fnameescape(s:sx)
endif
let &so = s:so_save | let &siso = s:siso_save
doautoall SessionLoadPost
unlet SessionLoad
" vim: set ft=vim :
