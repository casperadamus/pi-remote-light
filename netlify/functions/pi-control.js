// Netlify function to control Pi via HTTP request
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
    // For now, return success to test if the basic setup works
    // We'll add real Pi control after confirming the infrastructure works
    
    console.log('Pi control function called at:', new Date().toISOString());
    
    // Simulate Pi connection (replace with real SSH later)
    const simulateSuccess = Math.random() > 0.2; // 80% success rate for testing
    
    if (simulateSuccess) {
      return {
        statusCode: 200,
        headers,
        body: JSON.stringify({
          success: true,
          message: '✅ Test successful! Netlify function is working. (Real Pi control coming next)',
          timestamp: new Date().toISOString(),
          note: 'This is a test response - Pi SSH will be added once confirmed working'
        })
      };
    } else {
      return {
        statusCode: 200,
        headers,
        body: JSON.stringify({
          success: false,
          message: '⚠️ Simulated connection error (for testing). Function is working correctly.',
          timestamp: new Date().toISOString()
        })
      };
    }

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
