#!/usr/bin/env python3
"""
Simple HTTP server for Pi to receive light control commands from Netlify
Run this on your Raspberry Pi: python3 pi-server.py
"""

import json
import subprocess
import sys
from http.server import HTTPServer, BaseHTTPRequestHandler
from urllib.parse import urlparse
import logging

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(message)s')
logger = logging.getLogger(__name__)

class PiLightHandler(BaseHTTPRequestHandler):
    def do_POST(self):
        """Handle POST requests to control the light"""
        try:
            # Parse the URL
            parsed_path = urlparse(self.path)
            
            if parsed_path.path == '/toggle-light':
                # Read the request body
                content_length = int(self.headers.get('Content-Length', 0))
                if content_length > 0:
                    body = self.rfile.read(content_length)
                    data = json.loads(body.decode('utf-8'))
                    logger.info(f"Received light control request: {data}")
                
                # Execute the Python script to control the light
                try:
                    logger.info("Executing lighton.py script...")
                    result = subprocess.run(['python3', 'lighton.py'], 
                                          capture_output=True, 
                                          text=True, 
                                          timeout=10)
                    
                    if result.returncode == 0:
                        response_data = {
                            'success': True,
                            'message': 'Light toggled successfully!',
                            'output': result.stdout.strip() if result.stdout else '',
                            'timestamp': data.get('timestamp', '')
                        }
                        logger.info("Light script executed successfully")
                    else:
                        response_data = {
                            'success': False,
                            'message': 'Light script failed',
                            'error': result.stderr.strip() if result.stderr else 'Unknown error',
                            'returncode': result.returncode
                        }
                        logger.error(f"Light script failed with return code {result.returncode}")
                        
                except subprocess.TimeoutExpired:
                    response_data = {
                        'success': False,
                        'message': 'Light script timed out',
                        'error': 'Script execution took too long'
                    }
                    logger.error("Light script execution timed out")
                    
                except Exception as e:
                    response_data = {
                        'success': False,
                        'message': 'Error executing light script',
                        'error': str(e)
                    }
                    logger.error(f"Error executing light script: {e}")
                
                # Send response
                self.send_response(200)
                self.send_header('Content-Type', 'application/json')
                self.send_header('Access-Control-Allow-Origin', '*')
                self.send_header('Access-Control-Allow-Methods', 'POST, OPTIONS')
                self.send_header('Access-Control-Allow-Headers', 'Content-Type')
                self.end_headers()
                
                response_json = json.dumps(response_data)
                self.wfile.write(response_json.encode('utf-8'))
                
            else:
                # Invalid endpoint
                self.send_response(404)
                self.send_header('Content-Type', 'application/json')
                self.end_headers()
                self.wfile.write(json.dumps({'error': 'Endpoint not found'}).encode())
                
        except Exception as e:
            logger.error(f"Error handling POST request: {e}")
            self.send_response(500)
            self.send_header('Content-Type', 'application/json')
            self.end_headers()
            self.wfile.write(json.dumps({'error': 'Internal server error'}).encode())

    def do_OPTIONS(self):
        """Handle CORS preflight requests"""
        self.send_response(200)
        self.send_header('Access-Control-Allow-Origin', '*')
        self.send_header('Access-Control-Allow-Methods', 'POST, OPTIONS')
        self.send_header('Access-Control-Allow-Headers', 'Content-Type')
        self.end_headers()

    def do_GET(self):
        """Handle GET requests for status"""
        if self.path == '/status':
            self.send_response(200)
            self.send_header('Content-Type', 'application/json')
            self.send_header('Access-Control-Allow-Origin', '*')
            self.end_headers()
            
            status_data = {
                'status': 'Pi HTTP server is running',
                'timestamp': subprocess.check_output(['date']).decode().strip(),
                'endpoints': ['/toggle-light', '/status']
            }
            self.wfile.write(json.dumps(status_data).encode('utf-8'))
        else:
            self.send_response(404)
            self.end_headers()

    def log_message(self, format, *args):
        """Override to use our logger"""
        logger.info(f"{self.address_string()} - {format % args}")

def run_server(port=3000):
    """Start the HTTP server"""
    server_address = ('', port)
    httpd = HTTPServer(server_address, PiLightHandler)
    
    logger.info(f"Starting Pi HTTP server on port {port}")
    logger.info("Endpoints available:")
    logger.info("  POST /toggle-light - Toggle the light")
    logger.info("  GET  /status       - Server status")
    logger.info("Press Ctrl+C to stop the server")
    
    try:
        httpd.serve_forever()
    except KeyboardInterrupt:
        logger.info("Server stopped by user")
        httpd.server_close()

if __name__ == '__main__':
    port = 3000
    if len(sys.argv) > 1:
        try:
            port = int(sys.argv[1])
        except ValueError:
            logger.error("Invalid port number. Using default port 3000.")
    
    run_server(port)
