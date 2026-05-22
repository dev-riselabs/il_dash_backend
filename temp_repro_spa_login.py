import urllib.request
import urllib.error
import json
import http.cookiejar

cookie_jar = http.cookiejar.CookieJar()
opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(cookie_jar))

req = urllib.request.Request(
    'http://localhost:8001/sanctum/csrf-cookie',
    headers={'Origin': 'http://localhost:5173'}
)
with opener.open(req) as r:
    print('csrf status', r.status)
    for c in cookie_jar:
        print('cookie', c.name, c.value)

# Find the XSRF-TOKEN cookie and pass it as a header for login.
xsrf_token = None
for c in cookie_jar:
    if c.name == 'XSRF-TOKEN':
        xsrf_token = c.value

headers = {'Content-Type': 'application/json', 'Origin': 'http://localhost:5173'}
if xsrf_token:
    headers['X-XSRF-TOKEN'] = xsrf_token
    print('using X-XSRF-TOKEN from cookie')
else:
    print('no XSRF-TOKEN cookie found')

req = urllib.request.Request(
    'http://localhost:8001/api/auth/login',
    data=json.dumps({'email': 'x@example.com', 'password': 'x'}).encode('utf-8'),
    headers=headers
)
try:
    with opener.open(req) as r:
        print('login status', r.status)
        print('body', r.read().decode())
except urllib.error.HTTPError as e:
    print('login HTTPError', e.code)
    print(e.read().decode())
except Exception as e:
    import traceback
    traceback.print_exc()

# Test the protected auth/me endpoint using the same cookie jar.
req = urllib.request.Request(
    'http://localhost:8001/api/auth/me',
    headers={'Origin': 'http://localhost:5173'}
)
try:
    with opener.open(req) as r:
        print('me status', r.status)
        print('me body', r.read().decode())
except urllib.error.HTTPError as e:
    print('me HTTPError', e.code)
    print(e.read().decode())
except Exception as e:
    import traceback
    traceback.print_exc()
