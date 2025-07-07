// Chọn kích thước sản phẩm
document.querySelectorAll('.size-btn').forEach(button => {
    button.addEventListener('click', () => {
      // Bỏ chọn kích thước trước đó
      document.querySelectorAll('.size-btn').forEach(btn => btn.classList.remove('selected'));
      // Chọn kích thước mới
      button.classList.add('selected');
    });
  });
  
  // Xử lý nút "Add to cart"
  document.querySelector('.add-to-cart-btn').addEventListener('click', () => {
    alert('Item added to cart!');
  });
  
  // Xử lý nút "Buy"
  document.querySelector('.buy-btn').addEventListener('click', () => {
    alert('Redirecting to payment...');
  });
  