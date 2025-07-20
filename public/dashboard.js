
        function toggleMonthDropdown() {
            const dropdown = document.getElementById('monthDropdown');
            dropdown.classList.toggle('show');
        }

        function selectMonth(month) {
            // Update the selected month
            const options = document.querySelectorAll('.month-option');
            options.forEach(option => {
                option.classList.remove('selected');
                if (option.textContent === month) {
                    option.classList.add('selected');
                }
            });

            // Update the month tabs to show the selected month
            const monthTabs = document.querySelectorAll('.month-tabs-center .month-tab');
            monthTabs.forEach(tab => tab.classList.remove('active'));

            // Find if the selected month is in the current tabs (May, June, July)
            const currentMonths = ['May', 'June', 'July'];
            if (currentMonths.includes(month)) {
                const targetTab = Array.from(monthTabs).find(tab => tab.textContent === month);
                if (targetTab) {
                    targetTab.classList.add('active');
                }
            } else {
                // If it's a different month, update the tabs to show relevant months
                updateMonthTabs(month);
            }

            // Close the dropdown
            document.getElementById('monthDropdown').classList.remove('show');

            // Update the data for the selected month (you can customize this)
            updateDataForMonth(month);
        }

        function updateMonthTabs(selectedMonth) {
            const monthTabsCenter = document.querySelector('.month-tabs-center');
            const monthTabs = monthTabsCenter.querySelectorAll('.month-tab');
            const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            const selectedIndex = months.indexOf(selectedMonth);

            // Show 3 months centered around the selected month
            let startIndex = Math.max(0, selectedIndex - 1);
            let endIndex = Math.min(months.length - 1, selectedIndex + 1);

            // Adjust if we're at the beginning or end
            if (selectedIndex === 0) {
                endIndex = 2;
            } else if (selectedIndex === months.length - 1) {
                startIndex = months.length - 3;
            }

            // Update the month tabs
            monthTabs[0].textContent = months[startIndex];
            monthTabs[1].textContent = months[startIndex + 1];
            monthTabs[2].textContent = months[startIndex + 2];

            // Set the active tab
            monthTabs.forEach(tab => tab.classList.remove('active'));
            const activeIndex = selectedIndex - startIndex;
            monthTabs[activeIndex].classList.add('active');
        }

        function updateDataForMonth(month) {
            // Update chart and data based on selected month
            const chartAmount = document.querySelector('.chart-amount');
            const flowChanges = document.querySelectorAll('.flow-change');
            
            // Sample data for different months (you can customize this)
            const monthData = {
                'January': { total: '1,450 pts', inChange: '3,200 pts in Dec', outChange: '2,900 pts in Dec' },
                'February': { total: '1,350 pts', inChange: '3,100 pts in Jan', outChange: '3,050 pts in Jan' },
                'March': { total: '1,280 pts', inChange: '2,950 pts in Feb', outChange: '2,800 pts in Feb' },
                'April': { total: '1,380 pts', inChange: '3,150 pts in Mar', outChange: '2,950 pts in Mar' },
                'May': { total: '1,250 pts', inChange: '2,900 pts in Apr', outChange: '2,750 pts in Apr' },
                'June': { total: '1,350 pts', inChange: '3,050 pts in May', outChange: '2,850 pts in May' },
                'July': { total: '1,172 pts', inChange: '2,717 pts in Jun', outChange: '2,782 pts in Jun' },
                'August': { total: '1,400 pts', inChange: '3,200 pts in Jul', outChange: '3,100 pts in Jul' },
                'September': { total: '1,320 pts', inChange: '3,050 pts in Aug', outChange: '2,950 pts in Aug' },
                'October': { total: '1,480 pts', inChange: '3,300 pts in Sep', outChange: '3,150 pts in Sep' },
                'November': { total: '1,380 pts', inChange: '3,150 pts in Oct', outChange: '3,050 pts in Oct' },
                'December': { total: '1,550 pts', inChange: '3,450 pts in Nov', outChange: '3,250 pts in Nov' }
            };

            if (monthData[month]) {
                chartAmount.textContent = monthData[month].total;
                flowChanges[0].textContent = monthData[month].inChange;
                flowChanges[1].textContent = monthData[month].outChange;
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('monthDropdown');
            const button = document.querySelector('.month-button');
            
            if (!dropdown.contains(event.target) && !button.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });
