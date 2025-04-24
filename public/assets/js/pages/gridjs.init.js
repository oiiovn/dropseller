console.log("💡 JavaScript đang chạy...");


document.addEventListener("DOMContentLoaded", function () {
    console.log("Đang fetch dữ liệu từ Laravel...");

    if (document.getElementById("table-gridjs")) {
        fetch("http://127.0.0.1:8000/tat-ca-giao-dich")
            .then(response => {
                console.log("Response nhận được:", response);
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("Dữ liệu từ API:", data);

                let tableData = [];

                // Duyệt từng nhóm referral_code để tạo mảng dữ liệu cho Grid.js
                Object.keys(data).forEach(referralCode => {
                    data[referralCode].forEach(transaction => {
                        tableData.push([
                            transaction.id,
                            referralCode, // Lưu mã Referral vào bảng
                            transaction.description,
                            transaction.amount,
                            new Date(transaction.transaction_date).toLocaleString("vi-VN")
                        ]);
                    });
                });

                console.log("Dữ liệu sau khi xử lý:", tableData);

                new gridjs.Grid({
                    columns: [
                        { name: "ID", width: "80px" },
                        { name: "Referral Code", width: "150px" },
                        { name: "Description", width: "250px" },
                        { name: "Amount", width: "120px" },
                        { name: "Transaction Date", width: "180px" }
                    ],
                    pagination: { limit: 5 },
                    sort: true,
                    search: true,
                    data: tableData
                }).render(document.getElementById("table-gridjs"));
            })
            .catch(error => {
                console.error("Lỗi khi fetch dữ liệu:", error);
            });
    }
});
