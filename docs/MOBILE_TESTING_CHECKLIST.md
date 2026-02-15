# Mobile Testing Checklist

Use this checklist to verify mobile responsiveness across different devices and browsers.

## Pre-Testing Setup

- [ ] Run `npm run build` to compile latest assets
- [ ] Clear browser cache
- [ ] Enable developer tools and device emulation

## Desktop Testing (1920x1080)

### Homepage
- [ ] Layout displays correctly
- [ ] Navigation menu is accessible
- [ ] All content is readable

### Admin Panel
- [ ] Dashboard loads properly
- [ ] Widgets display in multi-column layout
- [ ] Tables are readable
- [ ] Forms are accessible
- [ ] Sidebar navigation works

### App Panel
- [ ] Player stats widget displays (2x2 grid)
- [ ] Quest tracker loads
- [ ] Inventory grid shows all items
- [ ] Navigation works properly

## Tablet Testing (iPad - 768x1024)

### Portrait Orientation
- [ ] Navigation adapts appropriately
- [ ] Widgets adjust to smaller grid
- [ ] Tables remain readable
- [ ] Forms are usable
- [ ] Touch targets are adequate (44px minimum)

### Landscape Orientation
- [ ] Content takes advantage of wider screen
- [ ] No unnecessary horizontal scrolling
- [ ] Widgets display properly

## Mobile Testing (iPhone 12 - 390x844)

### Portrait Orientation

#### Homepage
- [ ] Welcome page loads correctly
- [ ] Login/Register buttons are accessible
- [ ] Logo displays properly
- [ ] No horizontal scrolling

#### Admin Panel
- [ ] Login page is mobile-friendly
- [ ] Dashboard loads
- [ ] Sidebar menu is accessible
- [ ] Stats display in single/double column
- [ ] Tables can scroll horizontally or stack vertically
- [ ] Touch targets are minimum 44x44px
- [ ] No text zooming on input focus

#### App Panel

**Player Stats Widget**
- [ ] Displays in 2-column grid
- [ ] All stats are visible
- [ ] Icons render properly
- [ ] Text is readable (16px minimum)
- [ ] Touch-friendly card design

**Quest Tracker**
- [ ] Quest cards display properly
- [ ] Description is readable
- [ ] Progress bars show correctly
- [ ] Badges are visible
- [ ] Action buttons work

**Inventory Widget**
- [ ] Grid displays (3 columns on mobile)
- [ ] Slots are touch-friendly
- [ ] Empty slots show placeholder
- [ ] Occupied slots are highlighted
- [ ] Item icons display correctly
- [ ] Quantity badges show when > 1

**Social Links**
- [ ] Links display in responsive grid
- [ ] Buttons are touch-friendly
- [ ] Icons render properly

### Landscape Orientation
- [ ] Layout adjusts to wider viewport
- [ ] Content is accessible
- [ ] Navigation works

### Touch Interactions
- [ ] Buttons show active state on tap
- [ ] No accidental double-taps
- [ ] Scrolling is smooth
- [ ] Forms don't cause zoom
- [ ] Links are tappable

## Small Phone Testing (iPhone SE - 375x667)

- [ ] All content fits without horizontal scroll
- [ ] Touch targets remain adequate
- [ ] Text remains readable
- [ ] Inventory grid adjusts (3 columns)
- [ ] Navigation is accessible

## Large Phone Testing (Samsung Galaxy S20 - 360x800)

- [ ] Layout displays correctly
- [ ] Touch targets are adequate
- [ ] No horizontal scrolling
- [ ] Forms are usable

## Cross-Browser Testing

### iOS Safari
- [ ] Renders correctly
- [ ] No zoom on input focus
- [ ] Safe area insets work (notched devices)
- [ ] Touch feedback works
- [ ] Animations are smooth

### Chrome Mobile (Android)
- [ ] Renders correctly
- [ ] Touch interactions work
- [ ] Scrolling is smooth
- [ ] Dark mode works

### Firefox Mobile
- [ ] Layout displays correctly
- [ ] Interactive elements work
- [ ] Performance is acceptable

### Samsung Internet
- [ ] Compatible rendering
- [ ] Touch interactions work
- [ ] No layout issues

## Specific Feature Tests

### Navigation
- [ ] Bottom navigation is accessible (if implemented)
- [ ] Menu toggle works
- [ ] Safe area insets respected
- [ ] Menu items are tappable

### Forms
- [ ] Input fields are at least 44px tall
- [ ] Labels are visible and readable
- [ ] Validation messages display
- [ ] Submit buttons are accessible
- [ ] No zoom on focus (16px font minimum)

### Tables
- [ ] Mobile: Tables stack into cards with labels
- [ ] Desktop: Tables display in standard format
- [ ] Horizontal scroll works in wrapper
- [ ] Data-label attributes show on mobile
- [ ] Action buttons are accessible

### Cards
- [ ] Display with appropriate spacing
- [ ] Shadow renders correctly
- [ ] Content is readable
- [ ] Interactive cards have hover/active states

### Dark Mode
- [ ] Switches based on system preference
- [ ] All colors have dark mode variants
- [ ] Contrast is maintained
- [ ] Icons are visible

## Performance Testing

### Mobile Performance
- [ ] Page loads in < 3 seconds on 4G
- [ ] Scrolling is smooth (60fps)
- [ ] Animations don't stutter
- [ ] No layout shifts during load
- [ ] Touch response is immediate

### Network Conditions
- [ ] Test on slow 3G
- [ ] Test on 4G
- [ ] Test on WiFi
- [ ] Assets load progressively

## Accessibility Testing

### Touch Accessibility
- [ ] All interactive elements are 44x44px minimum
- [ ] Adequate spacing between touch targets
- [ ] Visual feedback on interaction
- [ ] No accidental activations

### Visual Accessibility
- [ ] Text contrast meets WCAG AA
- [ ] Font sizes are readable
- [ ] Icons have labels/tooltips
- [ ] Error messages are clear

### Screen Reader Testing (Optional)
- [ ] VoiceOver (iOS) works
- [ ] TalkBack (Android) works
- [ ] Semantic HTML is used
- [ ] ARIA labels where needed

## Edge Cases

### Device Orientation
- [ ] Handles rotation smoothly
- [ ] Layout adapts appropriately
- [ ] No content loss on rotation

### Notched Devices (iPhone X+)
- [ ] Safe area insets apply
- [ ] Content doesn't overlap notch
- [ ] Navigation respects bottom safe area

### Foldable Devices
- [ ] Layout adapts to fold
- [ ] No content is hidden when folded
- [ ] Unfold transitions smoothly

## Issue Documentation

When you find an issue, document:

1. **Device**: Model and OS version
2. **Browser**: Name and version
3. **Issue**: What's wrong
4. **Steps to reproduce**: How to see the issue
5. **Screenshot**: Visual proof
6. **Expected**: What should happen
7. **Actual**: What actually happens

## Sign-Off

- [ ] All critical issues fixed
- [ ] Tested on minimum 3 devices
- [ ] Tested on minimum 2 browsers
- [ ] Performance is acceptable
- [ ] Accessibility standards met
- [ ] Documentation updated

**Tester Name**: _______________
**Date**: _______________
**Approval**: _______________
