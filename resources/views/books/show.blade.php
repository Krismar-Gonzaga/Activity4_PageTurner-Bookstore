@extends('layouts.app')

@section('title', $book->title . ' - PageTurner')

@section('content')
<style>
    :root {
        --pageturner-primary: #8B4513;
        --pageturner-secondary: #D2691E;
        --pageturner-accent: #F4A460;
        --pageturner-light: #F5EBDC;
        --pageturner-very-light: #FDF8F0;
        --pageturner-dark: #5D4037;
        --pageturner-text: #3E2723;
        --shadow-card: 0 10px 28px rgba(139,69,19,0.12);
        --radius: 14px;
        --transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Page Turner Font */
    .page-turner-font {
        font-family: 'Playfair Display', Georgia, serif;
    }

    /* Book Container */
    .book-container {
        border-radius: 1rem;
        overflow: hidden;
        position: relative;
        border: 1px solid rgba(139,69,19,0.15);
        background: var(--pageturner-light);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Book Spine Effect */
    .book-spine {
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 0.75rem;
        background: linear-gradient(to bottom, #5D4037, #8B4513);
    }

    /* Book Layout */
    .book-layout {
        display: block;
        position: relative;
        z-index: 10;
    }

    @media (min-width: 768px) {
        .book-layout {
            display: flex;
        }
    }

    /* Cover Section */
    .cover-section {
        background: linear-gradient(to bottom right, var(--pageturner-light), var(--pageturner-accent));
        padding: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    @media (min-width: 768px) {
        .cover-section {
            width: 40%;
            padding: 3rem;
        }
    }

    .cover-wrapper {
        position: relative;
        display: inline-block;
    }

    .cover-image {
        max-height: 24rem;
        object-fit: contain;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s;
    }

    .cover-wrapper:hover .cover-image {
        transform: scale(1.05);
    }

    .cover-overlay {
        position: absolute;
        inset: 0;
        background: black;
        opacity: 0;
        transition: opacity 0.3s;
        border-radius: 0.5rem;
    }

    .cover-wrapper:hover .cover-overlay {
        opacity: 0.05;
    }

    .no-cover {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .no-cover-icon {
        height: 12rem;
        width: 12rem;
        color: #9ca3af;
    }

    .no-cover-text {
        margin-top: 1rem;
        color: #6b7280;
    }

    /* Details Section */
    .details-section {
        padding: 2rem;
        background: rgba(255, 255, 255, 0.7);
    }

    @media (min-width: 768px) {
        .details-section {
            width: 60%;
            padding: 3rem;
        }
    }

    /* Category Badge */
    .category-badge {
        display: inline-block;
        background: var(--pageturner-light);
        color: var(--pageturner-primary);
        font-size: 0.875rem;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        transition: all 0.3s;
        text-decoration: none;
    }

    .category-badge:hover {
        background: var(--pageturner-accent);
        color: var(--pageturner-dark);
    }

    /* Admin Action Buttons */
    .admin-actions {
        display: flex;
        gap: 0.5rem;
    }

    .edit-button {
        display: inline-flex;
        align-items: center;
        background: #fefce8;
        color: #a16207;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        transition: all 0.3s;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
    }

    .edit-button:hover {
        background: #fef9c3;
    }

    .delete-button {
        display: inline-flex;
        align-items: center;
        background: #fef2f2;
        color: #b91c1c;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        transition: all 0.3s;
        font-size: 0.875rem;
        font-weight: 500;
        border: none;
        cursor: pointer;
    }

    .delete-button:hover {
        background: #fee2e2;
    }

    .button-icon {
        width: 1rem;
        height: 1rem;
        margin-right: 0.5rem;
    }

    /* Book Title */
    .book-title {
        font-size: 2.25rem;
        font-weight: 700;
        color: #111827;
        margin-top: 0.5rem;
    }

    .book-author {
        font-size: 1.25rem;
        color: #4b5563;
        margin-top: 0.5rem;
    }

    .author-name {
        font-weight: 500;
    }

    /* Rating Stars */
    .rating-container {
        display: flex;
        align-items: center;
        margin-top: 1rem;
    }

    .stars-container {
        display: flex;
    }

    .star-filled {
        width: 1.5rem;
        height: 1.5rem;
        color: #fbbf24;
        fill: currentColor;
    }

    .star-half-wrapper {
        position: relative;
        width: 1.5rem;
        height: 1.5rem;
    }

    .star-half-background {
        position: absolute;
        width: 1.5rem;
        height: 1.5rem;
        color: #d1d5db;
        fill: currentColor;
    }

    .star-half-foreground {
        position: absolute;
        width: 1.5rem;
        height: 1.5rem;
        color: #fbbf24;
        fill: currentColor;
        clip-path: inset(0 50% 0 0);
    }

    .star-empty {
        width: 1.5rem;
        height: 1.5rem;
        color: #d1d5db;
        fill: currentColor;
    }

    .rating-text {
        margin-left: 0.75rem;
        color: #374151;
        font-weight: 500;
    }

    .rating-count {
        color: #6b7280;
        font-weight: 400;
    }

    /* Price Section */
    .price-section {
        margin-top: 1.5rem;
    }

    .price-wrapper {
        display: flex;
        align-items: baseline;
    }

    .price {
        font-size: 2.25rem;
        font-weight: 700;
        color: #8B4513;
    }

    .free-shipping-badge {
        margin-left: 0.75rem;
        font-size: 0.875rem;
        color: #16a34a;
        background: #f0fdf4;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
    }

    /* Stock Status */
    .stock-status {
        margin-top: 1rem;
    }

    .in-stock {
        display: flex;
        align-items: center;
    }

    .stock-indicator {
        width: 0.75rem;
        height: 0.75rem;
        border-radius: 9999px;
        margin-right: 0.5rem;
    }

    .in-stock .stock-indicator {
        background: #22c55e;
    }

    .in-stock-text {
        color: #15803d;
        font-weight: 500;
    }

    .low-stock-text {
        color: #ca8a04;
    }

    .stock-count {
        color: #4b5563;
    }

    .out-of-stock {
        display: flex;
        align-items: center;
    }

    .out-of-stock .stock-indicator {
        background: #ef4444;
    }

    .out-of-stock-text {
        color: #b91c1c;
        font-weight: 500;
    }

    .stock-notify {
        color: #4b5563;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    /* Book Info Grid */
    .info-grid {
        margin-top: 2rem;
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    @media (min-width: 768px) {
        .info-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .info-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .info-list {
        margin-top: 0.75rem;
        list-style: none;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .info-item {
        display: flex;
    }

    .info-label {
        color: #4b5563;
        width: 6rem;
    }

    .info-value {
        font-weight: 500;
    }

    /* Action Buttons */
    .action-buttons {
        margin-top: 2rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    @media (min-width: 640px) {
        .action-buttons {
            flex-direction: row;
        }
    }

    .add-to-cart-btn {
        flex: 1;
        width: 100%;
        background: #16a34a;
        color: white;
        padding: 1rem 2rem;
        border-radius: 0.75rem;
        transition: background 0.3s;
        font-weight: 700;
        font-size: 1.125rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
    }

    @media (min-width: 640px) {
        .add-to-cart-btn {
            width: auto;
        }
    }

    .add-to-cart-btn:hover {
        background: #15803d;
    }

    .wishlist-btn {
        flex: 1;
        width: 100%;
        border: 2px solid #8B4513;
        color: #8B4513;
        padding: 1rem 2rem;
        border-radius: 0.75rem;
        transition: all 0.3s;
        font-weight: 700;
        font-size: 1.125rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        cursor: pointer;
    }

    @media (min-width: 640px) {
        .wishlist-btn {
            width: auto;
        }
    }

    .wishlist-btn:hover {
        background: #F5EBDC;
    }

    .out-of-stock-btn {
        width: 100%;
        background: #d1d5db;
        color: #6b7280;
        padding: 1rem 2rem;
        border-radius: 0.75rem;
        font-weight: 700;
        font-size: 1.125rem;
        cursor: not-allowed;
        border: none;
    }

    .notify-btn {
        width: 100%;
        border: 2px solid #8B4513;
        color: #8B4513;
        padding: 1rem 2rem;
        border-radius: 0.75rem;
        transition: all 0.3s;
        font-weight: 700;
        font-size: 1.125rem;
        background: transparent;
        cursor: pointer;
    }

    .notify-btn:hover {
        background: #F5EBDC;
    }

    .btn-icon {
        width: 1.5rem;
        height: 1.5rem;
        margin-right: 0.75rem;
    }

    /* Description Section */
    .description-section {
        border-top: 1px solid #f3f4f6;
        padding: 2rem;
    }

    @media (min-width: 768px) {
        .description-section {
            padding: 3rem;
        }
    }

    .description-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--pageturner-dark);
        margin-bottom: 1.5rem;
    }

    .description-content {
        color: #374151;
        line-height: 1.625;
    }

    /* Reviews Section */
    .reviews-section {
        margin-top: 3rem;
    }

    .reviews-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .reviews-title {
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--pageturner-dark);
        font-family: 'Playfair Display', Georgia, serif;
    }

    .rating-summary {
        text-align: right;
    }

    .rating-summary-score {
        font-size: 2.25rem;
        font-weight: 700;
        color: var(--pageturner-primary);
    }

    .rating-summary-text {
        color: #4b5563;
    }

    .rating-summary-count {
        font-size: 0.875rem;
        color: #6b7280;
    }

    /* Review Form */
    .review-form {
        background: var(--pageturner-very-light);
        border-radius: 1rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(139,69,19,0.12);
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .review-form-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--pageturner-dark);
        margin-bottom: 1.5rem;
    }

    .rating-input {
        margin-bottom: 1.5rem;
    }

    .rating-label {
        display: block;
        color: var(--pageturner-dark);
        font-weight: 500;
        margin-bottom: 0.75rem;
    }

    .stars-input {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .star-input-hidden {
        display: none;
    }

    .star-label {
        cursor: pointer;
    }

    .star-icon {
        width: 2.5rem;
        height: 2.5rem;
        color: #d1d5db;
        transition: color 0.3s;
    }

    .star-label:hover .star-icon {
        color: #fbbf24;
    }

    .error-message {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }

    .comment-input {
        margin-bottom: 1.5rem;
    }

    .comment-label {
        display: block;
        color: var(--pageturner-dark);
        font-weight: 500;
        margin-bottom: 0.75rem;
    }

    .comment-textarea {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
        padding: 0.75rem;
    }

    .comment-textarea:focus {
        outline: none;
        ring: 2px solid var(--pageturner-accent);
        border-color: var(--pageturner-primary);
    }

    .submit-review-btn {
        display: flex;
        justify-content: flex-end;
    }

    .submit-btn {
        background: var(--pageturner-primary);
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 0.5rem;
        transition: background 0.3s;
        font-weight: 500;
        border: none;
        cursor: pointer;
    }

    .submit-btn:hover {
        background: var(--pageturner-secondary);
    }

    /* Login Prompt */
    .login-prompt {
        background: linear-gradient(to right, var(--pageturner-light), var(--pageturner-accent));
        border-radius: 1rem;
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .prompt-content {
        display: flex;
        align-items: center;
    }

    .prompt-icon {
        width: 3rem;
        height: 3rem;
        color: #8B4513;
        margin-right: 1rem;
    }

    .prompt-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--pageturner-dark);
    }

    .prompt-text {
        color: #374151;
        margin-top: 0.25rem;
    }

    .prompt-link {
        color: var(--pageturner-primary);
        font-weight: 500;
        text-decoration: none;
        transition: color 0.3s;
    }

    .prompt-link:hover {
        color: var(--pageturner-secondary);
    }

    /* Reviews List */
    .reviews-list {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .review-card {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(139,69,19,0.12);
        padding: 1.5rem;
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .review-user {
        display: flex;
        align-items: center;
    }

    .user-avatar {
        width: 3rem;
        height: 3rem;
        background: #F5EBDC;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
    }

    .user-initial {
        color: #8B4513;
        font-weight: 700;
        font-size: 1.125rem;
    }

    .user-info {
        display: flex;
        flex-direction: column;
    }

    .user-name {
        font-weight: 700;
        color: #111827;
    }

    .review-meta {
        display: flex;
        align-items: center;
        margin-top: 0.25rem;
    }

    .review-stars {
        display: flex;
        gap: 0.125rem;
    }

    .review-star {
        width: 1rem;
        height: 1rem;
    }

    .review-star.filled {
        color: #fbbf24;
        fill: currentColor;
    }

    .review-star.empty {
        color: #d1d5db;
        fill: currentColor;
    }

    .review-date {
        margin-left: 0.5rem;
        color: #6b7280;
    }

    .delete-review-btn {
        color: #ef4444;
        transition: color 0.3s;
        background: none;
        border: none;
        cursor: pointer;
    }

    .delete-review-btn:hover {
        color: #b91c1c;
    }

    .delete-icon {
        width: 1.25rem;
        height: 1.25rem;
    }

    .review-comment {
        color: #374151;
        white-space: pre-wrap;
    }

    /* Empty State */
    .empty-reviews {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        border: 1px solid #f3f4f6;
        padding: 3rem;
        text-align: center;
    }

    .empty-icon {
        width: 4rem;
        height: 4rem;
        color: #9ca3af;
        margin: 0 auto 1rem;
    }

    .empty-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.5rem;
    }

    .empty-text {
        color: #4b5563;
    }

    /* Spacing Utilities */
    .mt-12 {
        margin-top: 3rem;
    }

    .mb-8 {
        margin-bottom: 2rem;
    }

    .mb-6 {
        margin-bottom: 1.5rem;
    }

    .mb-4 {
        margin-bottom: 1rem;
    }

    .mt-2 {
        margin-top: 0.5rem;
    }

    .mt-1 {
        margin-top: 0.25rem;
    }

    .mt-4 {
        margin-top: 1rem;
    }

    .mr-2 {
        margin-right: 0.5rem;
    }

    .mr-3 {
        margin-right: 0.75rem;
    }

    .mr-4 {
        margin-right: 1rem;
    }

    .ml-2 {
        margin-left: 0.5rem;
    }

    .ml-3 {
        margin-left: 0.75rem;
    }

    /* Flex Utilities */
    .flex {
        display: flex;
    }

    .items-center {
        align-items: center;
    }

    .items-start {
        align-items: flex-start;
    }

    .justify-between {
        justify-content: space-between;
    }

    .justify-end {
        justify-content: flex-end;
    }

    .justify-center {
        justify-content: center;
    }

    .gap-2 {
        gap: 0.5rem;
    }

    .gap-4 {
        gap: 1rem;
    }

    .gap-6 {
        gap: 1.5rem;
    }

    .space-x-1 > * + * {
        margin-left: 0.25rem;
    }

    .space-x-2 > * + * {
        margin-left: 0.5rem;
    }

    .space-y-2 > * + * {
        margin-top: 0.5rem;
    }

    .space-y-6 > * + * {
        margin-top: 1.5rem;
    }

    /* Grid Utilities */
    .grid {
        display: grid;
    }

    .grid-cols-1 {
        grid-template-columns: repeat(1, 1fr);
    }

    /* Width Utilities */
    .w-full {
        width: 100%;
    }

    .w-3 {
        width: 0.75rem;
    }

    .w-4 {
        width: 1rem;
    }

    .w-5 {
        width: 1.25rem;
    }

    .w-6 {
        width: 1.5rem;
    }

    .w-10 {
        width: 2.5rem;
    }

    .w-12 {
        width: 3rem;
    }

    .w-16 {
        width: 4rem;
    }

    .w-24 {
        width: 6rem;
    }

    .w-48 {
        width: 12rem;
    }

    /* Height Utilities */
    .h-3 {
        height: 0.75rem;
    }

    .h-4 {
        height: 1rem;
    }

    .h-5 {
        height: 1.25rem;
    }

    .h-6 {
        height: 1.5rem;
    }

    .h-10 {
        height: 2.5rem;
    }

    .h-12 {
        height: 3rem;
    }

    .h-16 {
        height: 4rem;
    }

    .h-48 {
        height: 12rem;
    }

    /* Text Utilities */
    .text-sm {
        font-size: 0.875rem;
    }

    .text-lg {
        font-size: 1.125rem;
    }

    .text-xl {
        font-size: 1.25rem;
    }

    .text-2xl {
        font-size: 1.5rem;
    }

    .text-3xl {
        font-size: 1.875rem;
    }

    .text-4xl {
        font-size: 2.25rem;
    }

    .font-medium {
        font-weight: 500;
    }

    .font-bold {
        font-weight: 700;
    }

    .text-gray-300 {
        color: #d1d5db;
    }

    .text-gray-400 {
        color: #9ca3af;
    }

    .text-gray-500 {
        color: #6b7280;
    }

    .text-gray-600 {
        color: #4b5563;
    }

    .text-gray-700 {
        color: #374151;
    }

    .text-gray-900 {
        color: #111827;
    }

    .text-white {
        color: white;
    }

    .text-red-500 {
        color: #ef4444;
    }

    .text-red-700 {
        color: #b91c1c;
    }

    .text-green-600 {
        color: #16a34a;
    }

    .text-green-700 {
        color: #15803d;
    }

    .text-yellow-400 {
        color: #fbbf24;
    }

    .text-yellow-600 {
        color: #ca8a04;
    }

    .text-yellow-700 {
        color: #a16207;
    }

    /* Background Utilities */
    .bg-white {
        background: white;
    }

    .bg-gray-300 {
        background: #d1d5db;
    }

    .bg-gray-800 {
        background: #1f2937;
    }

    .bg-green-50 {
        background: #f0fdf4;
    }

    .bg-green-500 {
        background: #22c55e;
    }

    .bg-green-600 {
        background: #16a34a;
    }

    .bg-red-50 {
        background: #fef2f2;
    }

    .bg-red-500 {
        background: #ef4444;
    }

    .bg-yellow-50 {
        background: #fefce8;
    }

    /* Border Utilities */
    .border {
        border: 1px solid;
    }

    .border-t {
        border-top: 1px solid;
    }

    .border-gray-100 {
        border-color: #f3f4f6;
    }

    .border-gray-300 {
        border-color: #d1d5db;
    }

    .rounded-lg {
        border-radius: 0.5rem;
    }

    .rounded-xl {
        border-radius: 0.75rem;
    }

    .rounded-2xl {
        border-radius: 1rem;
    }

    .rounded-full {
        border-radius: 9999px;
    }

    /* Shadow Utilities */
    .shadow-sm {
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .shadow-md {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .shadow-lg {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    /* Object Fit */
    .object-contain {
        object-fit: contain;
    }

    /* Overflow */
    .overflow-hidden {
        overflow: hidden;
    }

    /* Position */
    .absolute {
        position: absolute;
    }

    .relative {
        position: relative;
    }

    .inset-0 {
        inset: 0;
    }

    .left-0 {
        left: 0;
    }

    .top-0 {
        top: 0;
    }

    .bottom-0 {
        bottom: 0;
    }

    .z-10 {
        z-index: 10;
    }

    /* Cursor */
    .cursor-pointer {
        cursor: pointer;
    }

    .cursor-not-allowed {
        cursor: not-allowed;
    }

    /* Transitions */
    .transition-colors {
        transition: all 0.3s;
    }

    .transition-transform {
        transition: transform 0.3s;
    }

    .transition-opacity {
        transition: opacity 0.3s;
    }

    .duration-300 {
        transition-duration: 300ms;
    }

    /* Hover Effects */
    .hover\:scale-105:hover {
        transform: scale(1.05);
    }

    .hover\:bg-gray-100:hover {
        background: #f3f4f6;
    }

    .hover\:bg-green-700:hover {
        background: #15803d;
    }

    .hover\:bg-red-100:hover {
        background: #fee2e2;
    }

    .hover\:bg-yellow-100:hover {
        background: #fef9c3;
    }

    .hover\:text-red-700:hover {
        color: #b91c1c;
    }

    /* Focus States */
    .focus\:outline-none:focus {
        outline: none;
    }

    .focus\:ring-2:focus {
        box-shadow: 0 0 0 2px var(--pageturner-accent);
    }

    .focus\:border-var\(--pageturner-primary\):focus {
        border-color: var(--pageturner-primary);
    }
</style>

<!-- Book Details Section -->
<div class="book-container">
    <!-- Book spine effect -->
    <div class="book-spine"></div>
    
    <div class="book-layout">
        <!-- Book Cover -->
        <div class="cover-section">
            @if($book->cover_image)
                <div class="cover-wrapper">
                    <img src="{{ asset('storage/' . $book->cover_image) }}" 
                         alt="{{ $book->title }}" 
                         class="cover-image">
                    <div class="cover-overlay"></div>
                </div>
            @else
                <div class="no-cover">
                    <svg class="no-cover-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                        </path>
                    </svg>
                    <span class="no-cover-text">Cover not available</span>
                </div>
            @endif
        </div>
        
        <!-- Book Details -->
        <div class="details-section">
            <!-- Category and Actions -->
            <div class="flex justify-between items-start mb-4">
                @if($book->category)
                    <a href="{{ route('categories.show', $book->category) }}" class="category-badge">
                        {{ $book->category->name }}
                    </a>
                @endif
                
                <!-- Admin Actions -->
                @auth
                    @if(auth()->user()->isAdmin())
                        <div class="admin-actions">
                            <a href="{{ route('admin.books.edit', $book) }}" class="edit-button">
                                <svg class="button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </a>
                            <form action="{{ route('admin.books.destroy', $book) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this book?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-button">
                                    <svg class="button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>
            
            <!-- Title and Author -->
            <h1 class="book-title">{{ $book->title }}</h1>
            <p class="book-author">by <span class="author-name">{{ $book->author }}</span></p>
            
            <!-- Rating -->
            <div class="rating-container">
                <div class="stars-container">
                    @php
                        $averageRating = $book->average_rating ?? 0;
                        $fullStars = floor($averageRating);
                        $hasHalfStar = ($averageRating - $fullStars) >= 0.5;
                        $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                    @endphp
                    
                    @for($i = 1; $i <= $fullStars; $i++)
                        <svg class="star-filled" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                    
                    @if($hasHalfStar)
                        <div class="star-half-wrapper">
                            <svg class="star-half-background" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="star-half-foreground" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                    @endif
                    
                    @for($i = 1; $i <= $emptyStars; $i++)
                        <svg class="star-empty" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <span class="rating-text">
                    {{ number_format($averageRating, 1) }} 
                    <span class="rating-count">({{ $book->reviews_count ?? $book->reviews->count() }} reviews)</span>
                </span>
            </div>
            
            <!-- Price and Stock -->
            <div class="price-section">
                <div class="price-wrapper">
                    <span class="price">
                        ${{ number_format($book->price, 2) }}
                    </span>
                    @if($book->price > 50)
                        <span class="free-shipping-badge">
                            Free Shipping
                        </span>
                    @endif
                </div>
                
                <div class="stock-status">
                    @if($book->stock_quantity > 0)
                        <div class="in-stock">
                            <div class="stock-indicator"></div>
                            <span class="in-stock-text">
                                In Stock 
                                @if($book->stock_quantity <= 10)
                                    <span class="low-stock-text">- Only {{ $book->stock_quantity }} left!</span>
                                @else
                                    <span class="stock-count">- {{ $book->stock_quantity }} available</span>
                                @endif
                            </span>
                        </div>
                    @else
                        <div class="out-of-stock">
                            <div class="stock-indicator"></div>
                            <span class="out-of-stock-text">Out of Stock</span>
                        </div>
                        <p class="stock-notify">We'll notify you when this book is back in stock.</p>
                    @endif
                </div>
            </div>
            
            <!-- Book Information -->
            <div class="info-grid">
                <div>
                    <h3 class="info-title">Details</h3>
                    <ul class="info-list">
                        <li class="info-item">
                            <span class="info-label">ISBN:</span>
                            <span class="info-value">{{ $book->isbn }}</span>
                        </li>
                        <li class="info-item">
                            <span class="info-label">Pages:</span>
                            <span class="info-value">{{ $book->pages ?? 'N/A' }}</span>
                        </li>
                        <li class="info-item">
                            <span class="info-label">Publisher:</span>
                            <span class="info-value">{{ $book->publisher ?? 'N/A' }}</span>
                        </li>
                        <li class="info-item">
                            <span class="info-label">Published:</span>
                            <span class="info-value">{{ $book->published_year ?? 'N/A' }}</span>
                        </li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="info-title">Format</h3>
                    <ul class="info-list">
                        <li class="info-item">
                            <span class="info-label">Binding:</span>
                            <span class="info-value">{{ $book->binding ?? 'Paperback' }}</span>
                        </li>
                        <li class="info-item">
                            <span class="info-label">Language:</span>
                            <span class="info-value">{{ $book->language ?? 'English' }}</span>
                        </li>
                        
                    </ul>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="action-buttons">
                @if($book->stock_quantity > 0)
                    <form action="{{ route('cart.add', $book) }}" method="POST" class="flex-1 flex items-center gap-2">
                        @csrf
                        <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden" style="display: none;">
                            
                            <input type="number" 
                                name="quantity" 
                                id="quantity" 
                                value="1" 
                                min="1" 
                                max="{{ $book->stock_quantity }}"
                                class="w-16 text-center border-0 focus:ring-0 quantity-input" 
                                required>
                            
                        </div>
                        @auth
                            @if(!auth()->user()->isAdmin())
                                <button type="submit" class="add-to-cart-btn flex-1">
                                    <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Add to Cart - ${{ number_format($book->price, 2) }}
                                </button>
                            @endif
                        @endauth
                    </form>
                    
                    
                @else
                    <button type="button" disabled class="out-of-stock-btn">
                        Out of Stock
                    </button>
                    <button type="button" class="notify-btn">
                        Notify When Available
                    </button>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Description Section -->
    @if($book->description)
        <div class="description-section">
            <h3 class="description-title">Description</h3>
            <div class="description-content">
                {!! nl2br(e($book->description)) !!}
            </div>
        </div>
    @endif
</div>

<!-- Reviews Section -->
<div class="reviews-section">
    <div class="reviews-header">
        <h2 class="reviews-title">Customer Reviews</h2>
        <div class="rating-summary">
            <div class="rating-summary-score">{{ number_format($averageRating, 1) }}</div>
            <div class="rating-summary-text">out of 5 stars</div>
            <div class="rating-summary-count">{{ $book->reviews_count ?? 0 }} reviews</div>
        </div>
    </div>
    
    <!-- Review Form (for authenticated users) -->
    @auth
        @php
            $hasPurchased = auth()->user()->hasPurchasedBook($book->id);
            $hasReviewed = $book->reviews->contains('user_id', auth()->id());
        @endphp
        
        @if($hasPurchased)
            @if(!$hasReviewed)
                <div class="review-form">
                    <h3 class="review-form-title">Share Your Thoughts</h3>
                    <form action="{{ route('reviews.store', $book) }}" method="POST">
                        @csrf
                        
                        <!-- Rating -->
                        <div class="rating-input">
                            <label class="rating-label">Your Rating</label>
                            <div class="stars-input" id="rating-stars">
                                @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" 
                                        id="star-{{ $i }}" 
                                        name="rating" 
                                        value="{{ $i }}" 
                                        class="star-input-hidden"
                                        {{ old('rating') == $i ? 'checked' : '' }}>
                                    <label for="star-{{ $i }}" class="star-label">
                                        <svg class="star-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                        </svg>
                                    </label>
                                @endfor
                            </div>
                            @error('rating')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Comment -->
                        <div class="comment-input">
                            <label for="comment" class="comment-label">Your Review</label>
                            <textarea id="comment" 
                                    name="comment" 
                                    rows="5"
                                    class="comment-textarea" 
                                    placeholder="Share your experience with this book...">{{ old('comment') }}</textarea>
                            @error('comment')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Submit -->
                        <div class="submit-review-btn">
                            <button type="submit" class="submit-btn">
                                Submit Review
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        @endif
    @else
        <div class="login-prompt">
            <div class="prompt-content">
                <svg class="prompt-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <div>
                    <h3 class="prompt-title">Want to share your thoughts?</h3>
                    <p class="prompt-text">
                        <a href="{{ route('login') }}" class="prompt-link">Login</a> 
                        or 
                        <a href="{{ route('register') }}" class="prompt-link">create an account</a> 
                        to write a review.
                    </p>
                </div>
            </div>
        </div>
    @endauth
    
    <!-- Display Reviews -->
    <div class="reviews-list">
        @forelse($book->reviews as $review)
            <div class="review-card">
                <div class="review-header">
                    <div class="review-user">
                        <div class="user-avatar">
                            <span class="user-initial">
                                {{ substr($review->user->name, 0, 1) }}
                            </span>
                        </div>
                        <div class="user-info">
                            <h4 class="user-name">{{ $review->user->name }}</h4>
                            <div class="review-meta">
                                <div class="review-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="review-star {{ $i <= $review->rating ? 'filled' : 'empty' }}" 
                                             viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="review-date">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Delete Button (Owner or Admin) -->
                    @auth
                        @if(auth()->id() === $review->user_id || auth()->user()->isAdmin())
                            <form action="{{ route('reviews.destroy', $review) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this review?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-review-btn">
                                    <svg class="delete-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    @endauth
                </div>
                
                <!-- Review Comment -->
                @if($review->comment)
                    <p class="review-comment">{{ $review->comment }}</p>
                @endif
            </div>
        @empty
            <div class="empty-reviews">
                <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <h3 class="empty-title">No Reviews Yet</h3>
                <p class="empty-text">Be the first to share your thoughts about this book!</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Interactive rating stars
    document.addEventListener('DOMContentLoaded', function() {
        const ratingContainer = document.getElementById('rating-stars');
        if (ratingContainer) {
            const stars = ratingContainer.querySelectorAll('.star-label');
            const inputs = ratingContainer.querySelectorAll('input[type="radio"]');
            
            stars.forEach((star, index) => {
                star.addEventListener('mouseover', function() {
                    // Fill stars on hover
                    for (let i = 0; i <= index; i++) {
                        const svg = stars[i].querySelector('svg');
                        svg.classList.remove('text-gray-300');
                        svg.classList.add('text-yellow-400');
                    }
                    for (let i = index + 1; i < stars.length; i++) {
                        const svg = stars[i].querySelector('svg');
                        svg.classList.remove('text-yellow-400');
                        svg.classList.add('text-gray-300');
                    }
                });
                
                star.addEventListener('click', function() {
                    const inputId = this.getAttribute('for');
                    const input = document.getElementById(inputId);
                    input.checked = true;
                    
                    // Keep stars filled after selection
                    stars.forEach((s, i) => {
                        const svg = s.querySelector('svg');
                        if (i <= index) {
                            svg.classList.remove('text-gray-300');
                            svg.classList.add('text-yellow-400');
                        } else {
                            svg.classList.remove('text-yellow-400');
                            svg.classList.add('text-gray-300');
                        }
                    });
                });
            });
            
            // Reset on mouse leave if no star selected
            ratingContainer.addEventListener('mouseleave', function() {
                const checkedInput = ratingContainer.querySelector('input[type="radio"]:checked');
                if (!checkedInput) {
                    stars.forEach(star => {
                        const svg = star.querySelector('svg');
                        svg.classList.remove('text-yellow-400');
                        svg.classList.add('text-gray-300');
                    });
                } else {
                    // If a star is checked, keep it highlighted
                    const checkedIndex = Array.from(inputs).findIndex(input => input.checked);
                    stars.forEach((star, i) => {
                        const svg = star.querySelector('svg');
                        if (i <= checkedIndex) {
                            svg.classList.remove('text-gray-300');
                            svg.classList.add('text-yellow-400');
                        } else {
                            svg.classList.remove('text-yellow-400');
                            svg.classList.add('text-gray-300');
                        }
                    });
                }
            });
        }
    });
</script>
@endpush