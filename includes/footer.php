    </main>

    <!-- Footer -->
    <footer class="bg-light mt-5">
        <div class="container">
            <div class="row py-5">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="text-gradient fw-bold mb-3">
                        <i class="bi bi-chat-dots"></i> <?php echo SITE_NAME; ?>
                    </h5>
                    <p class="text-muted"><?php echo SITE_DESCRIPTION; ?></p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-decoration-none">
                            <i class="bi bi-facebook fs-5 text-primary"></i>
                        </a>
                        <a href="#" class="text-decoration-none">
                            <i class="bi bi-twitter fs-5 text-info"></i>
                        </a>
                        <a href="#" class="text-decoration-none">
                            <i class="bi bi-linkedin fs-5 text-primary"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="<?php echo url('public/index.php'); ?>" class="text-decoration-none text-muted">Home</a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo url('public/forum/topics.php'); ?>" class="text-decoration-none text-muted">Topics</a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo url('public/auth/login.php'); ?>" class="text-decoration-none text-muted">Login</a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo url('public/auth/register.php'); ?>" class="text-decoration-none text-muted">Register</a>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Support</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="#" class="text-decoration-none text-muted">Help Center</a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-decoration-none text-muted">Contact Us</a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-decoration-none text-muted">Privacy Policy</a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-decoration-none text-muted">Terms of Service</a>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Newsletter</h6>
                    <p class="text-muted small mb-3">Stay updated with our latest news and updates.</p>
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Your email">
                        <button class="btn btn-primary" type="button">
                            <i class="bi bi-send"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Copyright Section -->
        <div class="footer-copyright">
            <div class="container">
                <div class="text-center my-2">
                    <div>
                        <span>© 2025 . </span>
                        <span class="text-muted">Developed by </span>
                        <a href="https://rivertheme.com" class="fw-semibold text-decoration-none" target="_blank" rel="noopener">RiverTheme</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?php echo url('public/js/script.js'); ?>"></script>
</body>
</html>

