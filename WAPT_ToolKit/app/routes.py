from flask import Blueprint, render_template, request, jsonify
from app.scanners.sql_injection import test_sql_injection
from app.scanners.xss import test_xss
from app.scanners.csrf import test_csrf

main = Blueprint('main', __name__)

@main.route('/')
def index():
    return render_template('index.html')

@main.route('/scan', methods=['POST'])
def scan():
    url = request.form.get('url')
    param = request.form.get('param')
    scan_type = request.form.get('scan_type')
    
    if scan_type == 'sql_injection':
        results = test_sql_injection(url, param)
    elif scan_type == 'xss':
        results = test_xss(url, param)
    elif scan_type == 'csrf':
        results = test_csrf(url)
    else:
        results = ["Invalid scan type"]
    
    return jsonify(results)
