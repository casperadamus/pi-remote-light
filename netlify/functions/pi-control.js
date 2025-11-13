const { exec } = require('child_process');

exports.handler = async (event, context) => {
  // Set CORS headers
  const headers = {
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Headers': 'Content-Type',
    'Access-Control-Allow-Methods': 'POST, OPTIONS',
    'Content-Type': 'application/json'
  };

  // Handle preflight OPTIONS request
  if (event.httpMethod === 'OPTIONS') {
    return {
      statusCode: 200,
      headers,
      body: ''
    };
  }

  if (event.httpMethod !== 'POST') {
    return {
      statusCode: 405,
      headers,
      body: JSON.stringify({ 
        success: false, 
        message: 'Method not allowed' 
      })
    };
  }

  try {
    // Pi connection details
    const PI_IP = '100.94.110.127';
    const PI_USER = 'casperadamus';
    const PI_PASS = '1016';
    const COMMAND = 'python3 lighton.py';

    // For security, you might want to use environment variables:
    // const PI_IP = process.env.PI_IP;
    // const PI_USER = process.env.PI_USER;
    // const PI_PASS = process.env.PI_PASS;

    // Use sshpass to connect and run command
    const sshCommand = `sshpass -p '${PI_PASS}' ssh -o StrictHostKeyChecking=no -o ConnectTimeout=5 ${PI_USER}@${PI_IP} '${COMMAND} > /tmp/script.log 2>&1 &'`;
    
    return new Promise((resolve) => {
      exec(sshCommand, { timeout: 10000 }, (error, stdout, stderr) => {
        if (error) {
          console.error('SSH Error:', error.message);
          resolve({
            statusCode: 200,
            headers,
            body: JSON.stringify({
              success: false,
              message: `Connection error: ${error.message}. Make sure Pi is online and accessible.`
            })
          });
        } else {
          resolve({
            statusCode: 200,
            headers,
            body: JSON.stringify({
              success: true,
              message: '✅ Light command sent to Pi successfully!',
              timestamp: new Date().toISOString()
            })
          });
        }
      });
    });

  } catch (error) {
    console.error('Function Error:', error);
    return {
      statusCode: 500,
      headers,
      body: JSON.stringify({
        success: false,
        message: 'Server error: ' + error.message
      })
    };
  }
};
