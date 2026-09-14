from urllib.request import build_opener,HTTPCookieProcessor,Request
from urllib.parse import urlencode
from urllib.error import HTTPError
from http.cookiejar import CookieJar
from bs4 import BeautifulSoup
import json,os
base=os.environ.get('BL_TEST_URL','http://127.0.0.1:8097'); client=build_opener(HTTPCookieProcessor(CookieJar()));checks=[]
def get(path):return client.open(base+path,timeout=20).read().decode()
def check(cond,msg):
 if not cond:raise AssertionError(msg)
 checks.append(msg);print('PASS',msg)
for path in ['/','/about/','/services/','/team/','/clients/','/contact/','/industries/','/our-team/','/team-member/toufic-moutran/']:
 s=BeautifulSoup(get(path),'html.parser');check(s.select_one('main') is not None,path+' renders');check(not s.select('a[href$=".html"]'),path+' has no static page links')
s=BeautifulSoup(get('/contact/'),'html.parser');f=s.select_one('form#contactForm');check(f and f.get('method')=='post' and 'admin-post.php' in f.get('action',''),'form uses WordPress handler')
def submit(extra):
 s=BeautifulSoup(get('/contact/'),'html.parser');f=s.select_one('form#contactForm');data={i['name']:i.get('value','') for i in f.select('input[name]')};data.update(firstName='Local',lastName='Test',email='local-test@example.test',company='=SUM(1,2)',phone='',interest='',message='Synthetic local test, no email sent.\nSecond line.');data.update(extra);return client.open(Request(f['action'],urlencode(data).encode()),timeout=20).read().decode()
h=submit({'bl_nonce':'bad'});check('Your form expired' in h,'invalid nonce rejected with feedback')
h=submit({'website_confirm':'spam'});check('Unable to submit' in h,'honeypot rejected')
h=submit({'email':'invalid'});check('Enter a valid email' in h and 'value="Local"' in h,'validation error retains safe input')
h=submit({});check('Your message has been received' in h,'valid submission saved and success displayed')
h=submit({});check('Your message has been received' in h,'duplicate submit handled safely')
for path in ['/wp-admin/admin-post.php?action=bl_export','/wp-json/wp/v2/bl_submission']:
 try:client.open(base+path);check(False,'anonymous restricted '+path)
 except HTTPError as e:check(e.code in [400,401,403,404],'anonymous restricted '+path)
print('Completed',len(checks),'HTTP checks')
# Exercise administrator detail and export endpoints using only the disposable local account.
get('/wp-login.php')
client.open(Request(base+'/wp-login.php',urlencode({'log':os.environ.get('BL_TEST_ADMIN','bl_test_admin'),'pwd':os.environ.get('BL_TEST_PASSWORD','Local-test-only-8297!'),'wp-submit':'Log In','redirect_to':base+'/wp-admin/','testcookie':'1'}).encode()),timeout=20).read()
s=BeautifulSoup(get('/wp-admin/admin.php?page=bl-submissions'),'html.parser')
check(s.find('h1',string='Form Submissions') is not None,'administrator submission list visible')
link=s.select_one('a[href*="submission="]');check(link is not None,'stored submission appears in list')
detail=client.open(link['href'],timeout=20).read().decode();check('Synthetic' in detail,'submission detail retains message')
export=s.select_one('a[href*="action=bl_export"]');check(export and '_wpnonce=' in export['href'],'CSV link carries nonce')
import csv,io
csv_text=client.open(export['href'],timeout=20).read().decode('utf-8-sig');rows=list(csv.reader(io.StringIO(csv_text)))
check(rows[0][:3]==['ID','Form type','Submitted at (site timezone)'],'CSV has clear headings')
check(any("'=SUM(1,2)" in row for row in rows[1:]),'export neutralizes stored formula value')
try:client.open(base+'/wp-admin/admin-post.php?action=bl_export&_wpnonce=bad');check(False,'invalid export nonce blocked')
except HTTPError as e:check(e.code==403,'invalid export nonce blocked')
print('Completed',len(checks),'HTTP checks including authenticated export')
