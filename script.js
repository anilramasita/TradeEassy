// Initial variables
let portfolio = [];
let transactionHistory = [];
const PIN = "1234";

// New function to show only one section at a time
function showOnlySection(sectionToShow) {
    const sections = [
        'buy-stock-section',
        'sell-stock-section',
        'portfolio-section',
        'transaction-section',
        'profit-loss-section'
    ];
// Hide all sections
sections.forEach(sectionId => {
    document.getElementById(sectionId).style.display = 'none';
});
// Clear the buy stock and sell stock chart links when navigating away
if (sectionToShow !== 'buy-stock-section') {
    document.getElementById("chart-link").innerHTML = "";
    document.getElementById("buy-message").innerHTML = ""; // Clear buy message
}
if (sectionToShow !== 'sell-stock-section') {
    document.getElementById("chart-link2").innerHTML = "";
    document.getElementById("sell-message").innerHTML = ""; // Clear sell message
}
// Show the requested section
if (sectionToShow) {
    document.getElementById(sectionToShow).style.display = 'block';
}
}

// Login functionality
document.getElementById('login-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const pin = document.getElementById('pin').value;
        if (pin === PIN) {
            document.getElementById('login-message').innerHTML = "Login successful!";
            document.getElementById('login-section').style.display = 'none';
            document.getElementById('trading-section').style.display = 'block';
        } else {
                document.getElementById('login-message').innerHTML = "Invalid PIN!";
            }
});

// Show Buy Stock form
document.getElementById('buy-stock-btn').addEventListener('click', function () {
    showOnlySection('buy-stock-section'); // Updated line
});

// Buy Stock functionality
document.getElementById('buy-stock-form').addEventListener('submit', function (e) {
    e.preventDefault();
    const ticker = document.getElementById('buy-ticker').value.toUpperCase();  // Stock Ticker
    const price = parseFloat(document.getElementById('buy-price').value);      // Buy Price
    const quantity = parseInt(document.getElementById('buy-quantity').value);  // Quantity to Buy

// Check if stock already exists in the portfolio
let stock = portfolio.find(s => s.ticker === ticker);

if (stock) {
// Update the stock's average price and total quantity
    const totalQuantity = stock.quantity + quantity;
    stock.price = ((stock.price * stock.quantity) + (price * quantity)) / totalQuantity;  // Weighted average price
    stock.quantity = totalQuantity;
    } else {
        // Add new stock to the portfolio
        portfolio.push({ ticker, price, quantity, totalQuantity: quantity });
    }

    transactionHistory.push({ type: 'buy', ticker, price, quantity });
            
    // Clear the buy form after submitting
    document.getElementById("buy-ticker").value = "";
    document.getElementById("buy-price").value = "";
    document.getElementById("buy-quantity").value = "";

    document.getElementById("buy-message").innerHTML = "Stock purchased successfully";
});


// Event listener for stock ticker input to generate the chart link
document.getElementById('buy-ticker').addEventListener('input', function () {
    const ticker = this.value.trim().toUpperCase(); // Get the input value
    const chartLinkDiv = document.getElementById('chart-link');

    if (ticker) {
        // Create the link based on the entered ticker
        const link = `https://in.tradingview.com/chart/?symbol=${ticker}`;
        chartLinkDiv.innerHTML = `<a href="${link}" target="_blank">View ${ticker} Chart</a>`;
    } else {
            // Clear the link if the input is empty
            chartLinkDiv.innerHTML = '';
    }
});
        

// Show Sell Stock form
document.getElementById('sell-stock-btn').addEventListener('click', function () {
    showOnlySection('sell-stock-section'); // Updated line
});

// Sell Stock functionality
document.getElementById('sell-stock-form').addEventListener('submit', function (e) {
    e.preventDefault();
    const ticker = document.getElementById('sell-ticker').value.toUpperCase();  // Stock Ticker
    const sellPrice = parseFloat(document.getElementById('sell-price').value);  // Sell Price
    const quantityToSell = parseInt(document.getElementById('sell-quantity').value);  // Quantity to Sell

    // Find the stock in the portfolio
    let stock = portfolio.find(s => s.ticker === ticker);

    if (!stock) {
        document.getElementById('sell-message').innerHTML = "Stock not found in portfolio!";
        return;
    }

    if (quantityToSell > stock.quantity) {
        document.getElementById('sell-message').innerHTML = `You only have ${stock.quantity} shares. Cannot sell ${quantityToSell} shares.`;
        return;
    }

    // Calculate profit or loss on the sale
    const profitLoss = (sellPrice - stock.price) * quantityToSell;

    // Reduce the stock quantity in the portfolio
    stock.quantity -= quantityToSell;
    if (stock.quantity === 0) {
        // Remove the stock from the portfolio if no shares are left
        portfolio = portfolio.filter(s => s.ticker !== ticker);
    }

    transactionHistory.push({ type: 'sell', ticker, price: sellPrice, quantity: quantityToSell, profitLoss });
            
    // Clear the sell form after submitting
    document.getElementById("sell-ticker").value = "";
    document.getElementById("sell-price").value = "";
    document.getElementById("sell-quantity").value = "";

    document.getElementById("sell-message").innerHTML = `Stock sold successfully. ${profitLoss > 0 ? 'Profit' : 'Loss'}: $${Math.abs(profitLoss).toFixed(2)}`;
});

// Event listener for stock ticker input to generate the chart link
document.getElementById('sell-ticker').addEventListener('input', function () {
    const ticker = this.value.trim().toUpperCase(); // Get the input value
    const chartLinkDiv = document.getElementById('chart-link2');

    if (ticker) {
        // Create the link based on the entered ticker
        const link = `https://in.tradingview.com/chart/?symbol=${ticker}`;
        chartLinkDiv.innerHTML = `<a href="${link}" target="_blank">View ${ticker} Chart</a>`;
    } else {
        // Clear the link if the input is empty
        chartLinkDiv.innerHTML = '';
    }
});

// View Portfolio
document.getElementById('view-portfolio-btn').addEventListener('click', function () {
    showOnlySection('portfolio-section'); // Updated line
    let portfolioHtml = portfolio.map(stock => `${stock.ticker}: ${stock.quantity} shares @ $${stock.price}`).join('<br>');
    document.getElementById('portfolio').innerHTML = portfolioHtml || "No stocks in portfolio.";
});

// View Transaction History
document.getElementById('view-transaction-btn').addEventListener('click', function () {
    showOnlySection('transaction-section'); // Show only the transaction section

    // Map over the transactionHistory array and generate a readable string for each transaction
    let historyHtml = transactionHistory.map(transaction => {
        if (transaction.type === 'buy') {
            return `Bought ${transaction.quantity} shares of ${transaction.ticker} at $${transaction.price}`;
        } else if (transaction.type === 'sell') {
            return `Sold ${transaction.quantity} shares of ${transaction.ticker} at $${transaction.price}`;
        }
    }).join('<br>');

    // If there are no transactions, show a message
    document.getElementById('transaction-history').innerHTML = historyHtml || "No transactions yet.";
});


// Check Profit/Loss for each stock when the "Check Profit/Loss" button is clicked
document.getElementById('profit-loss-btn').addEventListener('click', function () {
    showOnlySection('profit-loss-section');  // Show only the profit/loss section
    let totalProfitLoss = 0;  // Variable to accumulate total profit/loss for all stocks
    let profitLossMessages = "";  // String to store messages for each stock

    // Check transactions for all sell activities and calculate profit/loss
    transactionHistory.forEach(transaction => {
        if (transaction.type === 'sell') {
            const profitLossMessage = transaction.profitLoss > 0
            ? `Profit: Sold ${transaction.quantity} shares of ${transaction.ticker} at $${transaction.price}. Profit: $${transaction.profitLoss.toFixed(2)}`
            : `Loss: Sold ${transaction.quantity} shares of ${transaction.ticker} at $${transaction.price}. Loss: $${Math.abs(transaction.profitLoss).toFixed(2)}`;

            totalProfitLoss += transaction.profitLoss;
            profitLossMessages += profitLossMessage + "<br>";
        }
});

// Display total profit/loss or a message if there are no transactions
if (profitLossMessages) {
    document.getElementById('profit-loss-status').innerHTML = profitLossMessages + `<br>Total Profit/Loss: $${totalProfitLoss.toFixed(2)}`;
} else {
    document.getElementById('profit-loss-status').innerHTML = "No transactions to calculate profit or loss.";
}
});
// Open Live Stock Chart
document.getElementById('live-chart-btn').addEventListener('click', function () {
    window.open('https://in.tradingview.com/#main-market-summary', '_blank');
});